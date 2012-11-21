<?php
namespace TYPO3\Expose\Utility;

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
 * The array functions from the good old t3lib_div plus new code.
 *
 * @Flow\Scope("singleton")
 * @todo (robert) I'm not sure yet if we should use this library statically or as a singleton. The latter might be problematic if we use it from the Core classes.
 */
class Arrays extends \TYPO3\Flow\Utility\Arrays {

	/**
	 * Sort and array by positional arguments
	 *
	 * @param array $unsortedArray the array to sort
	 * @return array
	 */
	static public function sortPositionalArray(array &$unsortedArray, $positionalPath = '__meta.position') {
		$arrayKeysWithPosition = array();

		foreach ($unsortedArray as $key => $subElement) {
			if (self::getValueByPath($subElement, $positionalPath)) {
				$arrayKeysWithPosition[$key] = self::getValueByPath($subElement, $positionalPath);
			} else {
				if (is_numeric($key)) {
					$arrayKeysWithPosition[$key] = $key;
				} else {
					$arrayKeysWithPosition[$key] = 0;
				}
			}
		}

			// $startKeys, $middleKeys and $endKeys are multi-dimensional arrays:
			// the KEY of each array is a PRIORITY, the VALUE is an array of sub-TypoScript-Object KEYS
		$startKeys = array();
		$middleKeys = array();
		$endKeys = array();
			// $beforeKeys and $afterKeys are multi-dimensional arrays
			// the key of each array is another KEY, the VALUE is an array of PRIORITIES of sub-TypoScript-Object KEYS
		$beforeKeys = array();
		$afterKeys = array();

			// First, we parse the positional string and depending on this string, add the elements to the three arrays from above
		foreach ($arrayKeysWithPosition as $key => $position) {
			$matches = array();
			if (preg_match('/^start(?: ([0-9s]+))?$/', $position, $matches)) {
				if (isset($matches[1])) {
					$startKeys[intval($matches[1])][] = $key;
				} else {
					$startKeys[0][] = $key;
				}
			} elseif (preg_match('/^end(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[1])) {
					$endKeys[intval($matches[1])][] = $key;
				} else {
					$endKeys[0][] = $key;
				}
			} elseif (preg_match('/^before ([a-zA-Z0-9]+)(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[2])) {
					$beforeKeys[$matches[1]][$matches[2]][] = $key;
				} else {
					$beforeKeys[$matches[1]][0][] = $key;
				}
			} elseif (preg_match('/^after ([a-zA-Z0-9]+)(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[2])) {
					$afterKeys[$matches[1]][$matches[2]][] = $key;
				} else {
					$afterKeys[$matches[1]][0][] = $key;
				}
			} elseif (preg_match('/^[0-9]+$/', (string)$position)) {
				$middleKeys[intval($position)][] = $key;
			} else {
				throw new \TYPO3\TypoScript\Exception('The positional string "' . $position . '" is not supported.', 1345126502);
			}
		}

			// Now, sort the three arrays by priority key
		krsort($startKeys, SORT_NUMERIC);
		foreach ($beforeKeys as $key => &$keysByPriority) {
			krsort($keysByPriority, SORT_NUMERIC);
		}
		ksort($middleKeys, SORT_NUMERIC);
		foreach ($afterKeys as $key => &$keysByPriority) {
			ksort($keysByPriority, SORT_NUMERIC);
		}
		ksort($endKeys, SORT_NUMERIC);

			// Finally, collect all results and flatten them
		$prefinalKeys = array();
		$flattenerFunction = function($value, $key, $step) use (&$prefinalKeys, &$beforeKeys, &$afterKeys, &$flattenerFunction) {
			if (isset($beforeKeys[$value])) {
				array_walk_recursive($beforeKeys[$value], $flattenerFunction, $step);
				unset($beforeKeys[$value]);
			}
			$prefinalKeys[$step][] = $value;
			if (isset($afterKeys[$value])) {
				array_walk_recursive($afterKeys[$value], $flattenerFunction, $step);
				unset($afterKeys[$value]);
			}
		};

			// 1st step: collect regular keys and process before / after if keys occcured
		array_walk_recursive($startKeys, $flattenerFunction, 0);
		array_walk_recursive($middleKeys, $flattenerFunction, 2);
		array_walk_recursive($endKeys, $flattenerFunction, 4);

			// 2nd step: process before / after leftovers for unmatched keys
		array_walk_recursive($beforeKeys, $flattenerFunction, 1);
		array_walk_recursive($afterKeys, $flattenerFunction, 3);

		ksort($prefinalKeys);

			// 3rd step: mix everything together
		$finalKeys = array();
		array_walk_recursive($prefinalKeys, function($value) use (&$finalKeys) {
			$finalKeys[] = $value;
		});

		$sortedArray = array();
		foreach ($finalKeys as $key) {
			$sortedArray[$key] = $unsortedArray[$key];
		}

		return $sortedArray;
	}

}
?>