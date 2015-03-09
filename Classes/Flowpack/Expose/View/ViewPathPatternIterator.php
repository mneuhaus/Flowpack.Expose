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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Utility\Files;

/**
 * An iterator, used by TemplateView, that expands path patterns and iterates over the resulting set(s) of paths.
 *
 * This generates a set of paths, or a "fallback chain" of paths, for the given request's controller and each of the
 * extended controllers of that controller. This means that controllers can extend other controllers without copying all
 * of the extended controller's template files.
 *
 * An example of extended controllers:
 *
 * - Some.Package\Controller\FooController extends Some.Package\Controller\BarController
 *   FooController will automatically reuse BarController's templates.
 *   So, FooController does *not* need a duplicate copy of BarController's templates.
 *
 * - Another.Package\Controller\BazController extends Some.Package\Controller\FooController
 *   BazController will automatically reuse templates first from FooController, and then from BarController.
 *   Another.Package does *not* need to duplicate any of the templates provided in Some.Package.
 *
 * This iterator begins by getting a list including the given request's controller and any of its extended controllers.
 * It generates a "fallback chain" for each controller, beginning with the given request's controller, and then iterates
 * over all that fallback chain of paths. When it iterates past the end of one fallback chain, it generates the next
 * controller's fallback chain and iterates through that fallback chain's paths. This process (generate fallback chain,
 * then iterate through the paths) continues until there are no more extended controllers left to iterate through.
 *
 * There are several methods used to change how paths are generated:
 * - setBubbleControllerAndSubpackage()
 * - setFormatIsOptional()
 *
 * This class depends on these things:
 * - the objectManager (injected through injectObjectManager() )
 * - an actionRequest (injected through setRequest)
 * - the view's options array (injected through setViewOptions)
 *
 * @api
 */
class ViewPathPatternIterator implements \Iterator {

	/**
	 * @Flow\Inject
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * The current ActionRequest
	 *
	 * This iterator depends on the ActionRequest's info about the current controller and the request format.
	 *
	 * @var ActionRequest
	 */
	protected $request;

	/**
	 * The configuration options of the view
	 * @see TYPO3\Flow\View\Mvc\AbstractView->options
	 *
	 * @var array
	 */
	protected $viewOptions;

	/**
	 * If TRUE, then we successively split off parts from "@controller" and "@subpackage" until both are empty.
	 *
	 * @var boolean
	 */
	protected $bubbleControllerAndSubpackage = FALSE;

	/**
	 * If TRUE, then half of the resulting strings will have ."@format" stripped off, and the other half will have it.
	 *
	 * @var boolean
	 */
	protected $formatIsOptional;

	/**
	 * Pattern to be resolved.
	 *
	 * Typically something like: "@templateRoot/@subpackage/@controller/@action.@format"
	 *
	 * @var array
	 */
	protected $pattern;

	/**
	 * The paths generated from $this->pattern for the controllers in $this->controllers
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * The iterator position
	 *
	 * @var integer
	 */
	protected $position = 0;

	/**
	 * The list of controllers to generate fallback chains of paths for.
	 * Populated in rewind with the given request's controller and any of its extended controllers.
	 *
	 * @var array of controllerObjectNames
	 */
	protected $controllers = array();

	/**
	 * Constructs this Iterator with the given pattern.
	 *
	 * TODO: Should the other options/injection stuff be part of the constructor?
	 *
	 * @param string $pattern
	 */
	public function __construct($pattern) {
		$this->pattern = $pattern;
	}

	/**
	 * Inject the view's options
	 * @see TYPO3\Flow\View\Mvc\AbstractView->options
	 *
	 * @param array $viewOptions the View's options
	 */
	public function setViewOptions(array $viewOptions) {
		$this->viewOptions = $viewOptions;
	}

	/**
	 * Set whether or not to use BubbleControllerAndSubpackage mode when generating the fallback chains
	 *
	 * @param boolean $bubbleControllerAndSubpackage
	 */
	public function setBubbleControllerAndSubpackage($bubbleControllerAndSubpackage) {
		$this->bubbleControllerAndSubpackage = $bubbleControllerAndSubpackage;
	}

	/**
	 * Set whether or not to use FormatIsOptional mode when generating the fallback chains
	 *
	 * @param boolean $formatIsOptional
	 */
	public function setFormatIsOptional($formatIsOptional) {
		$this->formatIsOptional = $formatIsOptional;
	}

	/**
	 * Set the current ActionRequest which holds the required info about the controller and the request format.
	 *
	 * @param ActionRequest $request
	 */
	public function setRequest(ActionRequest $request) {
		$this->request = $request;
	}

	/**
	 * Returns the current path.
	 *
	 * @return mixed
	 * @api
	 */
	public function current() {
		return $this->paths[$this->position];
	}

	/**
	 * Move forward to the next path
	 *
	 * @return void
	 * @api
	 */
	public function next() {
		$this->position++;
	}

	/**
	 * Returns the identifier of the current path
	 *
	 * @return string
	 * @api
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Checks if current position of the view path pattern iterator is valid
	 *
	 * From http://php.net/manual/iterator.valid.php :
	 * "This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid."
	 *
	 * If there is not another path in $this-paths, the iterator generates a fallback chain for the next controller
	 * or extended controller. This only returns FALSE if the iterator has reached the end of the paths in any of the
	 * generated fallback chains, and there are no more controllers to generate fallback chains for.
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
				// Try to fetch the controller information from the ControllerObjectName
				$controllerPackageKey = $this->objectManager->getPackageKeyByObjectName($controllerObjectName);
				/*
				 * Match these:
				 * <packageKey>/Controller/<controllerName>Controller
				 * <packageKey>/<subpackageKey>/Controller/<controllerName>Controller
				 */
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

				// TODO: What happens if a controller extends a class that is not a controller?
				$controllerSubpackageKey = (isset($matches['subpackageKey'])) ? $matches['subpackageKey'] : NULL;
				$controllerName = $matches['controllerName'];
			} else {
				// Otherwise get the controller information from the request
				// This is the case right after a rewind.
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
	 * Rewind the view path pattern iterator to the first path
	 *
	 * From http://php.net/manual/en/iterator.rewind.php :
	 * "This is the first method called when executing a foreach loop."
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
	 * This method is used to expand $this-pattern into "fallback chains" for file system locations where the given
	 * controller's templates could reside.
	 *
	 * This processes the following placeholders inside $this->pattern:
	 *  - "@templateRoot"
	 *  - "@partialRoot"
	 *  - "@layoutRoot"
	 *  - "@subpackage"
	 *  - "@controller"
	 *  - "@format"
	 *
	 * For an explanation on the effects of $this->bubbleControllerAndSubpackage and $this->formatIsOptional:
	 * @see TemplateView->expandGenericPathPattern()
	 *
	 * @param string|null $controllerPackageKey The controller's package key
	 * @param string|null $controllerSubpackageKey The controller's subpackage key
	 * @param string|null $controllerName The name of the controller to expand path patterns for
	 * @return array of unix style paths: a "fallback chain" of possible template paths for the given controller
	 */
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

		$format = $this->request->getFormat();
		if ($this->formatIsOptional) {
			$this->expandPatterns($controllerPaths, '.@format', array('.' . $format, ''));
			$this->expandPatterns($controllerPaths, '@format', array($format, ''));
		} else {
			$this->expandPatterns($controllerPaths, '.@format', array('.' . $format));
			$this->expandPatterns($controllerPaths, '@format', array($format));
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

	/**
	 * Resolves the template root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return array Path(s) to template root directory
	 */
	protected function getTemplateRootPaths($controllerPackageKey) {
		if ($this->viewOptions['templateRootPaths'] !== NULL) {
			return $this->viewOptions['templateRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->viewOptions['templateRootPathPattern']));
	}

	/**
	 * Resolves the partial root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return array Path(s) to partial root directory
	 */
	protected function getPartialRootPaths($controllerPackageKey) {
		if ($this->viewOptions['partialRootPaths'] !== NULL) {
			return $this->viewOptions['partialRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->viewOptions['partialRootPathPattern']));
	}

	/**
	 * Resolves the layout root to be used inside other paths.
	 *
	 * @param string $controllerPackageKey
	 * @return string Path(s) to layout root directory
	 */
	protected function getLayoutRootPaths($controllerPackageKey) {
		if ($this->viewOptions['layoutRootPaths'] !== NULL) {
			return $this->viewOptions['layoutRootPaths'];
		}
		return array(str_replace('@packageResourcesPath', 'resource://' . $controllerPackageKey, $this->viewOptions['layoutRootPathPattern']));
	}

}
