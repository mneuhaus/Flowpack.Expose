<?php
namespace TYPO3\Expose\View;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\Flow\Mvc\Exception\InvalidTemplateResourceException;
use TYPO3\Flow\Utility\Files;
use TYPO3\Fluid\View\TemplateView;

/**
 * The main template view. Should be used as view if you want Fluid Templating
 *
 * @api
 */
class FallbackView extends TemplateView {
	/**
	 * Resolves the template root to be used inside other paths.
	 *
	 * @return array Path(s) to template root directory
	 */
	public function getTemplateRootPaths($controllerPackageKey = NULL) {
		if ($this->options['templateRootPaths'] !== NULL) {
			return $this->options['templateRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['templateRootPathPattern']));
	}

	/**
	 * Resolves the partial root to be used inside other paths.
	 *
	 * @return array Path(s) to partial root directory
	 */
	protected function getPartialRootPaths($controllerPackageKey = NULL) {
		if ($this->options['partialRootPaths'] !== NULL) {
			return $this->options['partialRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['partialRootPathPattern']));
	}

	/**
	 * Resolves the layout root to be used inside other paths.
	 *
	 * @return string Path(s) to layout root directory
	 */
	protected function getLayoutRootPaths($controllerPackageKey = NULL) {
		if ($this->options['layoutRootPaths'] !== NULL) {
			return $this->options['layoutRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['layoutRootPathPattern']));
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
		$paths = array();
		/** @var $actionRequest \TYPO3\Flow\Mvc\ActionRequest */
		$actionRequest = $this->controllerContext->getRequest();
		$controllerObjectName = $actionRequest->getControllerObjectName();

		$controllerObjectNames = array_merge(array($controllerObjectName), class_parents($controllerObjectName));
		foreach ($controllerObjectNames as $controllerObjectName) {
			if (substr($controllerObjectName, -9) === '_Original') {
				continue;
			}

			$controllerPackageKey = $this->objectManager->getPackageKeyByObjectName($controllerObjectName);
			$subject = substr($controllerObjectName, strlen($controllerPackageKey) + 1);
			preg_match('/
				^(
					Controller
				|
					(?P<subpackageKey>.+)\\\\Controller
				)
				\\\\(?P<controllerName>[a-z\\\\]+)Controller
				$/ix', $subject, $matches
			);
			$subpackageKey = (isset($matches['subpackageKey'])) ? $matches['subpackageKey'] : NULL;
			$controllerName = $matches['controllerName'];

			if ($subpackageKey === 'Mvc') {
				continue;
			}

			$paths = array_merge($paths, $this->expandGenericPathPatternForController($pattern, $controllerPackageKey, $subpackageKey, $controllerName, $bubbleControllerAndSubpackage, $formatIsOptional));
		}

		return array_values(array_unique($paths));
	}

	public function expandGenericPathPatternForController($pattern, $controllerPackageKey, $subpackageKey, $controllerName, $bubbleControllerAndSubpackage, $formatIsOptional) {
		$paths = array($pattern);
		/** @var $actionRequest \TYPO3\Flow\Mvc\ActionRequest */
		$actionRequest = $this->controllerContext->getRequest();

		$this->expandPatterns($paths, '@templateRoot', $this->getTemplateRootPaths($controllerPackageKey));
		$this->expandPatterns($paths, '@partialRoot', $this->getPartialRootPaths($controllerPackageKey));
		$this->expandPatterns($paths, '@layoutRoot', $this->getLayoutRootPaths($controllerPackageKey));

		if ($bubbleControllerAndSubpackage) {
			$numberOfPathsBeforeSubpackageExpansion = count($paths);
			$subpackageKeyParts = ($subpackageKey !== NULL) ? explode('\\', $subpackageKey) : array();
			$numberOfSubpackageParts = count($subpackageKeyParts);
			$subpackageReplacements = array();
			for ($i = 0; $i <= $numberOfSubpackageParts; $i++) {
				$subpackageReplacements[] = implode('/', ($i < 0 ? $subpackageKeyParts : array_slice($subpackageKeyParts, $i)));
			}
			$this->expandPatterns($paths, '@subpackage', $subpackageReplacements);

			for ($i = ($numberOfPathsBeforeSubpackageExpansion - 1) * ($numberOfSubpackageParts + 1); $i >= 0; $i -= ($numberOfSubpackageParts + 1)) {
				array_splice($paths, $i, 0, str_replace('@controller', $controllerName, $paths[$i]));
			}
			$this->expandPatterns($paths, '@controller', array(''));
		} else {
			$this->expandPatterns($paths, '@subpackage', array($subpackageKey));
			$this->expandPatterns($paths, '@controller', array($controllerName));
		}

		if ($formatIsOptional) {
			$this->expandPatterns($paths, '.@format', array('.' . $actionRequest->getFormat(), ''));
			$this->expandPatterns($paths, '@format', array($actionRequest->getFormat(), ''));
		} else {
			$this->expandPatterns($paths, '.@format', array('.' . $actionRequest->getFormat()));
			$this->expandPatterns($paths, '@format', array($actionRequest->getFormat()));
		}
		return $paths;
	}
}
