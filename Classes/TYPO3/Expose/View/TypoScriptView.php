<?php
namespace TYPO3\Expose\View;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TypoScript".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * View for using TypoScript for standard MVC controllers.
 *
 * Loads all TypoScript files from the current package Resources/Private/TypoScript
 * folder (recursively); and then checks whether a TypoScript object for current
 * controller and action can be found.
 *
 * If the controller class name is Foo\Bar\Baz\Controller\BlahController and the action is "index",
 * it checks for the TypoScript path Foo.Bar.Baz.BlahController.index.
 * If this path is found, then it is used for rendering. Otherwise, the $fallbackView
 * is used.
 */
class TypoScriptView extends \TYPO3\TypoScript\View\TypoScriptView {
	/**
	 *
	 * @return string
	 */
	public function getTypoScriptPath() {
		return $this->typoScriptPath;
	}

	/**
	 *
	 * @param string
	 */
	public function setTypoScriptPath($typoScriptPath) {
		$this->typoScriptPath = $typoScriptPath;
	}

	/**
	 * Returns the initialized TypoScript Runtime
	 *
	 * @return \TYPO3\TypoScript\Core\Runtime
	 */
	public function getTypoScriptRuntime() {
		$this->initializeTypoScriptRuntime();
		return $this->typoScriptRuntime;
	}


	/**
	 * ################################################
	 * The Lines below will be removed when the change:
	 * https://review.typo3.org/#/c/16394/ gets merged
	 * ################################################
	 */


	/**
	 * This contains the supported options, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedOptions = array(
		'typoScriptPathPatterns' => array(array('resource://@package/Private/TypoScripts/'), 'TypoScript files will be recursively loaded from this paths.', 'array'),
		'typoScriptPath' => array(NULL, 'The TypoScript path which should be rendered; derived from the controller and action names or set by the user.', 'string'),
		'packageKey' => array(NULL, 'The package key where the TypoScript should be loaded from. If not given, is automatically derived from the current request.', 'string')
	);

	/**
	 * The TypoScript Runtime
	 *
	 * @var \TYPO3\TypoScript\Core\Runtime
	 */
	protected $typoScriptRuntime = NULL;

	/**
	 * Set the TypoScript path from the options
	 */
	public function initializeObject() {
		$this->typoScriptPath = $this->getOption('typoScriptPath');
	}

	/**
	 * Render the view
	 *
	 * @return string The rendered view
	 * @api
	 */
	public function render() {
		$this->initializeTypoScriptRuntime();
		if ($this->typoScriptRuntime->canRender($this->typoScriptPath) || $this->fallbackViewEnabled === FALSE) {
			return $this->renderTypoScript();
		} else {
			return $this->renderFallbackView();
		}
	}

	/**
	 * Load the TypoScript Files form the defined
	 * paths and construct a Runtime from the
	 * parsed results
	 *
	 * @return void
	 */
	public function initializeTypoScriptRuntime() {
		if ($this->typoScriptRuntime === NULL) {
			$this->loadTypoScript();

			$this->initializeTypoScriptPathForCurrentRequest();

			$this->typoScriptRuntime = new \TYPO3\TypoScript\Core\Runtime($this->parsedTypoScript, $this->controllerContext);
		}
	}

	/**
	 * Load TypoScript from the directories specified by $this->getOption('typoScriptPathPatterns')
	 *
	 * @return void
	 */
	protected function loadTypoScript() {
		$mergedTypoScriptCode = '';
		$typoScriptPathPatterns = $this->getOption('typoScriptPathPatterns');
		ksort($typoScriptPathPatterns);
		foreach ($typoScriptPathPatterns as $typoScriptPathPattern) {
			$typoScriptPathPattern = str_replace('@package', $this->getPackageKey(), $typoScriptPathPattern);
			$filePaths = \TYPO3\Flow\Utility\Files::readDirectoryRecursively($typoScriptPathPattern, '.ts2');
			sort($filePaths);
			foreach ($filePaths as $filePath) {
				$mergedTypoScriptCode .= PHP_EOL . file_get_contents($filePath) . PHP_EOL;
			}
		}
		$this->parsedTypoScript = $this->typoScriptParser->parse($mergedTypoScriptCode);
	}

	/**
	 * Get the package key to load the TypoScript from. If set, $this->getOption('packageKey') is used.
	 * Otherwise, the current request is taken and the controller package key is extracted
	 * from there.
	 *
	 * @return string the package key to load TypoScript from
	 */
	protected function getPackageKey() {
		$packageKey = $this->getOption('packageKey');
		if ($packageKey !== NULL) {
			return $packageKey;
		} else {
			return $this->controllerContext->getRequest()->getControllerPackageKey();
		}
	}

	/**
	 * Initialize $this->typoScriptPath depending on the current controller and action
	 *
	 * @return void
	 */
	protected function initializeTypoScriptPathForCurrentRequest() {
		if ($this->typoScriptPath === NULL) {
			$request = $this->controllerContext->getRequest();
			$typoScriptPathForCurrentRequest = $request->getControllerObjectName();
			$typoScriptPathForCurrentRequest = str_replace('\\Controller\\', '\\', $typoScriptPathForCurrentRequest);
			$typoScriptPathForCurrentRequest = str_replace('\\', '/', $typoScriptPathForCurrentRequest);
			$typoScriptPathForCurrentRequest = trim($typoScriptPathForCurrentRequest, '/');
			$typoScriptPathForCurrentRequest .= '/' . $request->getControllerActionName();

			$this->typoScriptPath = $typoScriptPathForCurrentRequest;
		}
	}

	/**
	 * Render the given TypoScript and return the rendered page
	 *
	 * @return string
	 */
	protected function renderTypoScript() {
		$this->typoScriptRuntime->pushContextArray($this->variables);
		$output = $this->typoScriptRuntime->render($this->typoScriptPath);
		$this->typoScriptRuntime->popContext();
		return $output;
	}
}
?>