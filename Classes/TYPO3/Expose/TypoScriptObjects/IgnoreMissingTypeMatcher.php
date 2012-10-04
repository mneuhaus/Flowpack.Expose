<?php
namespace TYPO3\Expose\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A matcher which only matches if the target typoscript type is actually found
 */
class IgnoreMissingTypeMatcher extends \TYPO3\TypoScript\TypoScriptObjects\MatcherImplementation {

	/**
	 * @return mixed|string
	 */
	public function evaluate() {
		try {
			return parent::evaluate();
		} catch(\TYPO3\TypoScript\Exception $exception) {
			if ($exception->getCode() === 1332493995) {
					// BAD HACK!!
				return \TYPO3\TypoScript\TypoScriptObjects\CaseImplementation::MATCH_NORESULT;
			} else {
				throw $exception;
			}
		}
	}

}
?>