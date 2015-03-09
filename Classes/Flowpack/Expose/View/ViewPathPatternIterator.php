<?php
namespace Flowpack\Expose\View;

use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Object\ObjectManager;
use TYPO3\Flow\Utility\Files;
use TYPO3\Flow\Annotations as Flow;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * An iterator for path patterns used by the TemplateView
 *
 * @api
 */
class ViewPathPatternIterator implements \Iterator {
	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var boolean
	 */
	protected $bubbleControllerAndSubpackage = FALSE;

	/**
	 * @var boolean
	 */
	protected $formatIsOptional;

	/**
	 * @var ActionRequest
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var array
	 */
	protected $pattern;

	/**
	 * @var array
	 */
	protected $paths = array();

	/**
	 * @var string
	 */
	protected $position = 0;

	/**
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Constructs this Iterator
	 *
	 * @param string $controllerName
	 */
	public function __construct($pattern) {
		$this->pattern = $pattern;
	}

	public function injectObjectManager($objectManager) {
		$this->objectManager = $objectManager;
	}

	public function setOptions($options) {
		$this->options = $options;
	}

	public function setBubbleControllerAndSubpackage($bubbleControllerAndSubpackage) {
		$this->bubbleControllerAndSubpackage = $bubbleControllerAndSubpackage;
	}

	public function setFormatIsOptional($formatIsOptional) {
		$this->formatIsOptional = $formatIsOptional;
	}

	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * Returns the data of the current cache entry pointed to by the cache entry
	 * iterator.
	 *
	 * @return mixed
	 * @api
	 */
	public function current() {
		return $this->paths[$this->position];
	}

	/**
	 * Move forward to the next cache entry
	 *
	 * @return void
	 * @api
	 */
	public function next() {
		$this->position++;
	}

	/**
	 * Returns the identifier of the current cache entry pointed to by the cache
	 * entry iterator.
	 *
	 * @return string
	 * @api
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Checks if current position of the cache entry iterator is valid
	 *
	 * @return boolean TRUE if the current element of the iterator is valid, otherwise FALSE
	 * @api
	 */
	public function valid() {
		if (isset($this->paths[$this->position])) {
			return TRUE;
		}

        if (!isset($this->paths[$this->position]) && count($this->controllers) > 0) {
        	$controllerObjectName = array_shift($this->controllers);

        	if (class_exists($controllerObjectName)) {
	        	// Try to fetch the information from the ControllerObjectName
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

				$controllerSubpackageKey = (isset($matches['subpackageKey'])) ? $matches['subpackageKey'] : NULL;
				$controllerName = $matches['controllerName'];
			} else {
				$controllerName = $this->request->getControllerName();
				$controllerPackageKey = $this->request->getControllerPackageKey();
				$controllerSubpackageKey = $this->request->getControllerSubpackageKey();
			}

        	$additionalPaths = $this->expandGenericPathPatternFor($controllerPackageKey, $controllerSubpackageKey, $controllerName);
        	$this->paths = array_merge($this->paths, array_values(array_unique($additionalPaths)));
        }

        return isset($this->paths[$this->position]);
	}

	/**
	 * Rewind the cache entry iterator to the first element
	 *
	 * @return void
	 * @api
	 */
	public function rewind() {
		$this->position = 0;

		$controllerObjectName = $this->request->getControllerObjectName();
		$this->controllers = array($controllerObjectName);

		if (!class_exists($controllerObjectName)) {
			return;
		}

		foreach (class_parents($controllerObjectName) as $controllerObjectName) {
			if (substr($controllerObjectName, -9) === '_Original') {
				continue;
			}
			$this->controllers[] = $controllerObjectName;
		}
	}

	/**
	 * Resolves the template root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return array Path(s) to template root directory
	 */
	public function getTemplateRootPaths($controllerPackageKey) {
		if ($this->options['templateRootPaths'] !== NULL) {
			return $this->options['templateRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['templateRootPathPattern']));
	}

	/**
	 * Resolves the partial root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return array Path(s) to partial root directory
	 */
	protected function getPartialRootPaths($controllerPackageKey) {
		if ($this->options['partialRootPaths'] !== NULL) {
			return $this->options['partialRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['partialRootPathPattern']));
	}

	/**
	 * Resolves the layout root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return string Path(s) to layout root directory
	 */
	protected function getLayoutRootPaths($controllerPackageKey) {
		if ($this->options['layoutRootPaths'] !== NULL) {
			return $this->options['layoutRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->options['layoutRootPathPattern']));
	}

	protected function expandGenericPathPatternFor($controllerPackageKey = NULL, $controllerSubpackageKey = NULL, $controllerName = NULL) {
		$controllerPaths = array($this->pattern);

		$this->expandPatterns($controllerPaths, '@templateRoot', $this->getTemplateRootPaths($controllerPackageKey));
		$this->expandPatterns($controllerPaths, '@partialRoot', $this->getPartialRootPaths($controllerPackageKey));
		$this->expandPatterns($controllerPaths, '@layoutRoot', $this->getLayoutRootPaths($controllerPackageKey));

		if ($this->bubbleControllerAndSubpackage) {
			$numberOfPathsBeforeSubpackageExpansion = count($controllerPaths);
			$subpackageKeyParts = ($controllerSubpackageKey !== NULL) ? explode('\\', $controllerSubpackageKey) : array();
			$numberOfSubpackageParts = count($subpackageKeyParts);
			$subpackageReplacements = array();
			for ($i = 0; $i <= $numberOfSubpackageParts; $i++) {
				$subpackageReplacements[] = implode('/', ($i < 0 ? $subpackageKeyParts : array_slice($subpackageKeyParts, $i)));
			}
			$this->expandPatterns($controllerPaths, '@subpackage', $subpackageReplacements);

			for ($i = ($numberOfPathsBeforeSubpackageExpansion - 1) * ($numberOfSubpackageParts + 1); $i >= 0; $i -= ($numberOfSubpackageParts + 1)) {
				array_splice($controllerPaths, $i, 0, str_replace('@controller', $controllerName, $controllerPaths[$i]));
			}
			$this->expandPatterns($controllerPaths, '@controller', array(''));
		} else {
			$this->expandPatterns($controllerPaths, '@subpackage', array($controllerSubpackageKey));
			$this->expandPatterns($controllerPaths, '@controller', array($controllerName));
		}

		if ($this->formatIsOptional) {
			$this->expandPatterns($controllerPaths, '.@format', array('.' . $this->request->getFormat(), ''));
			$this->expandPatterns($controllerPaths, '@format', array($this->request->getFormat(), ''));
		} else {
			$this->expandPatterns($controllerPaths, '.@format', array('.' . $this->request->getFormat()));
			$this->expandPatterns($controllerPaths, '@format', array($this->request->getFormat()));
		}
		return $controllerPaths;
	}

	/**
	 * Expands the given $patterns by adding an array element for each $replacement
	 * replacing occurrences of $search.
	 *
	 * @param array $patterns
	 * @param string $search
	 * @param array $replacements
	 * @return void
	 */
	protected function expandPatterns(array &$patterns, $search, array $replacements) {
		$patternsWithReplacements = array();
		foreach ($patterns as $pattern) {
			foreach ($replacements as $replacement) {
				$patternsWithReplacements[] = Files::getUnixStylePath(str_replace($search, $replacement, $pattern));
			}
		}
		$patterns = $patternsWithReplacements;
	}

}
