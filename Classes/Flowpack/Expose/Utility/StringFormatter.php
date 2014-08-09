<?php
namespace Flowpack\Expose\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class StringFormatter {

	public static function formNameToPath($formName) {
		$parts = explode('[', $formName);
		array_walk($parts, function(&$value, $key){
			$value = trim($value, ']');
		});
		return implode('.', $parts);
	}

	public static function pathToFormId($path) {
		return str_replace('.', '-', $path);
	}
}
?>