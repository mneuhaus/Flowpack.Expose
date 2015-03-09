<?php
namespace Flowpack\Expose\View;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\Flow\Utility\Files;
use TYPO3\Fluid\View\TemplateView;

/**
 * The main template view. Should be used as view if you want Fluid Templating
 *
 * @api
 */
class InheritanceTemplateView extends TemplateView {
	/**
	 * Resolves the template root to be used inside other paths.
	 *
	 * By default this gets the templateRootPathPattern from the current package, but you can also use the optional
	 * $controllerPackageKey to set an alternate package to get the templateRootPathPattern from.
	 *
	 * @param string $controllerPackageKey optionally define an alternate PackageKey for the templateRootPathPattern
	 * @return array Path(s) to template root directory
	 */
	public function getTemplateRootPaths($controllerPackageKey = NULL) {
		if ($this->options['templateRootPaths'] !== NULL) {
			return $this->options['templateRootPaths'];
		}
		if ($controllerPackageKey === NULL) {
			/** @var $actionRequest \TYPO3\Flow\Mvc\ActionRequest */
			$actionRequest = $this->controllerContext->getRequest();
			$controllerPackageKey = $actionRequest->getControllerPackageKey();
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['templateRootPathPattern']));
	}

	/**
	 * Resolves the partial root to be used inside other paths.
	 *
	 * @return array Path(s) to partial root directory
	 */
	protected function getPartialRootPaths() {
		if ($this->options['partialRootPaths'] !== NULL) {
			return $this->options['partialRootPaths'];
		}
		/** @var ActionRequest $actionRequest */
		$actionRequest = $this->controllerContext->getRequest();
		$controllerPackageKey = $actionRequest->getControllerPackageKey();
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['partialRootPathPattern']));
	}

	/**
	 * Resolves the layout root to be used inside other paths.
	 *
	 * @return string Path(s) to layout root directory
	 */
	protected function getLayoutRootPaths() {
		if ($this->options['layoutRootPaths'] !== NULL) {
			return $this->options['layoutRootPaths'];
		}
		/** @var ActionRequest $actionRequest */
		$actionRequest = $this->controllerContext->getRequest();
		$controllerPackageKey = $actionRequest->getControllerPackageKey();
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['layoutRootPathPattern']));
	}

	/**
	 * Resolve the template path and filename for the given action. If $actionName
	 * is NULL, looks into the current request.
	 *
	 * @param string $actionName Name of the action. If NULL, will be taken from request.
	 * @return string Full path to template
	 * @throws Exception\InvalidTemplateResourceException
	 */
	protected function getTemplatePathAndFilename($actionName = NULL) {
		if ($this->options['templatePathAndFilename'] !== NULL) {
			return $this->options['templatePathAndFilename'];
		}
		if ($actionName === NULL) {
			/** @var $actionRequest \TYPO3\Flow\Mvc\ActionRequest */
			$actionRequest = $this->controllerContext->getRequest();
			$actionName = $actionRequest->getControllerActionName();
		}
		$actionName = ucfirst($actionName);

		$paths = $this->expandGenericPathPattern($this->options['templatePathAndFilenamePattern'], FALSE, FALSE);
		$tried = array();
		foreach ($paths as $templatePathAndFilename) {
			$templatePathAndFilename = str_replace('@action', $actionName, $templatePathAndFilename);
			$tried[] = $templatePathAndFilename;
			if (is_file($templatePathAndFilename)) {
				return $templatePathAndFilename;
			}
		}
		throw new Exception\InvalidTemplateResourceException('Template could not be loaded. I tried "' . implode('", "', $tried) . '"', 1225709595);
	}


	/**
	 * Resolve the path and file name of the layout file, based on
	 * $this->options['layoutPathAndFilename'] and $this->options['layoutPathAndFilenamePattern'].
	 *
	 * In case a layout has already been set with setLayoutPathAndFilename(),
	 * this method returns that path, otherwise a path and filename will be
	 * resolved using the layoutPathAndFilenamePattern.
	 *
	 * @param string $layoutName Name of the layout to use. If none given, use "Default"
	 * @return string Path and filename of layout files
	 * @throws Exception\InvalidTemplateResourceException
	 */
	protected function getLayoutPathAndFilename($layoutName = 'Default') {
		if ($this->options['layoutPathAndFilename'] !== NULL) {
			return $this->options['layoutPathAndFilename'];
		}
		$paths = $this->expandGenericPathPattern($this->options['layoutPathAndFilenamePattern'], TRUE, TRUE);
		$layoutName = ucfirst($layoutName);
		$tried = array();
		foreach ($paths as $layoutPathAndFilename) {
			$layoutPathAndFilename = str_replace('@layout', $layoutName, $layoutPathAndFilename);
			$tried[] = $layoutPathAndFilename;
			if (is_file($layoutPathAndFilename)) {
				return $layoutPathAndFilename;
			}
		}
		throw new Exception\InvalidTemplateResourceException('The layout files "' . implode('", "', $tried) . '" could not be loaded.', 1225709595);
	}

	/**
	 * Resolve the partial path and filename based on $this->options['partialPathAndFilenamePattern'].
	 *
	 * @param string $partialName The name of the partial
	 * @return string the full path which should be used. The path definitely exists.
	 * @throws Exception\InvalidTemplateResourceException
	 */
	protected function getPartialPathAndFilename($partialName) {
		$paths = $this->expandGenericPathPattern($this->options['partialPathAndFilenamePattern'], TRUE, TRUE);
		$tried = array();
		foreach ($paths as $partialPathAndFilename) {
			$partialPathAndFilename = str_replace('@partial', $partialName, $partialPathAndFilename);
			$tried[] = $partialPathAndFilename;
			if (is_file($partialPathAndFilename)) {
				return $partialPathAndFilename;
			}
		}
		throw new Exception\InvalidTemplateResourceException('The partial files "' . implode('", "', $tried) . '" could not be loaded.', 1225709595);
	}

	/**
	 * Processes following placeholders inside $pattern:
	 *  - "@templateRoot"
	 *  - "@partialRoot"
	 *  - "@layoutRoot"
	 *  - "@subpackage"
	 *  - "@controller"
	 *  - "@format"
	 *
	 * This method is used to generate "fallback chains" for file system locations where a certain Partial can reside.
	 *
	 * If $bubbleControllerAndSubpackage is FALSE and $formatIsOptional is FALSE, then the resulting array will only have one element
	 * with all the above placeholders replaced.
	 *
	 * If you set $bubbleControllerAndSubpackage to TRUE, then you will get an array with potentially many elements:
	 * The first element of the array is like above. The second element has the @ controller part set to "" (the empty string)
	 * The third element now has the @ controller part again stripped off, and has the last subpackage part stripped off as well.
	 * This continues until both "@subpackage" and "@controller" are empty.
	 *
	 * Example for $bubbleControllerAndSubpackage is TRUE, we have the MyCompany\MyPackage\MySubPackage\Controller\MyController
	 * as Controller Object Name and the current format is "html"
	 *
	 * If pattern is "@templateRoot/@subpackage/@controller/@action.@format", then the resulting array is:
	 *  - "Resources/Private/Templates/MySubPackage/My/@action.html"
	 *  - "Resources/Private/Templates/MySubPackage/@action.html"
	 *  - "Resources/Private/Templates/@action.html"
	 *
	 * If you set $formatIsOptional to TRUE, then for any of the above arrays, every element will be duplicated  - once with "@format"
	 * replaced by the current request format, and once with ."@format" stripped off.
	 *
	 * @param string $pattern Pattern to be resolved
	 * @param boolean $bubbleControllerAndSubpackage if TRUE, then we successively split off parts from "@controller" and "@subpackage" until both are empty.
	 * @param boolean $formatIsOptional if TRUE, then half of the resulting strings will have ."@format" stripped off, and the other half will have it.
	 * @return array unix style paths
	 */
	protected function expandGenericPathPattern($pattern, $bubbleControllerAndSubpackage, $formatIsOptional) {
		$iterator = new ViewPathPatternIterator($pattern);
		$iterator->setViewOptions($this->options);
		$iterator->setBubbleControllerAndSubpackage($bubbleControllerAndSubpackage);
		$iterator->setFormatIsOptional($formatIsOptional);
		$iterator->setRequest($this->controllerContext->getRequest());

		return $iterator;
	}

}
