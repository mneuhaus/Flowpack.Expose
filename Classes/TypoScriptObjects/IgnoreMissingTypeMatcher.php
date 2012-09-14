<?php
namespace TYPO3\Expose\TypoScriptObjects;

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
 * A matcher which only matches if the target typoscript type is actually found
 */
class IgnoreMissingTypeMatcher extends \TYPO3\TypoScript\TypoScriptObjects\Matcher {

	public function evaluate() {
		try {
			return parent::evaluate();
		} catch(\TYPO3\TypoScript\Exception $e) {
			if ($e->getCode() === 1332493995) {
					// BAD HACK!!
				return \TYPO3\TypoScript\TypoScriptObjects\CaseTsObject::MATCH_NORESULT;
			}
		}
	}
}
?>