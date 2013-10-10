<?php
namespace TYPO3\Expose\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Render a Form section using the Form framework
 */
class Schema extends \TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation {
	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$sortedChildTypoScriptKeys = $this->sortNestedTypoScriptKeys();

		if (count($sortedChildTypoScriptKeys) === 0) {
			return array();
		}

		$schema = array();

		foreach ($this->properties as $key => $value) {
			if ($key == 'properties') {
				$properties = $value;
				foreach ($properties as $propertyName => $values) {
					foreach ($values as $key => $value) {
						$value = $this->processPath($key, $value, $this->path . '/properties/' . $propertyName);
						$schema['properties'][$propertyName][$key] = $value;
					}
				}
			} else {
				$schema[$key] = $this->processPath($key, $value, $this->path);
			}
		}

		return $schema;
	}

	public function processPath($key, $value, $path) {
		if (isset($value['__eelExpression'])) {
			$result = $this->tsRuntime->evaluateProcessor($key, $this, $value);
		} elseif (isset($value['__objectType'])) {
			$result = $this->tsRuntime->evaluate($path . '/' . $key);
		} else {
			$result = $value;
		}
		return $result;
	}
}

?>