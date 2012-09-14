<?php
namespace TYPO3\Expose\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 *
 */
class NodeTreeProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {

	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {

		$this->result = $runtime->render($typoScriptPath . '/nodeTree<TYPO3.Expose:NodeTree>');
	}

	public function process($subject) {
		return str_replace("<placeholder:content/>", $subject, $this->result);
	}

	public function afterInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
	}
}
?>