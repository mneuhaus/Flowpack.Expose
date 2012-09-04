<?php
namespace TYPO3\Admin\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              		  *
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
 * TODO actually implement this feature
 */
class PaginationProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {
	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		$context = $runtime->getCurrentContext();
		if (isset($context['objects']) && count($context['objects']) > 3) {
			$runtime->pushContext('objects', array(
				$context['objects'][0],
				$context['objects'][1],
				$context['objects'][2]
			));
		}
	}
	public function process($subject) {
		return $subject;
	}

	public function afterInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		$runtime->popContext();
	}
}
?>