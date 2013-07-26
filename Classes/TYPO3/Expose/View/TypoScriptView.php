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
}
?>