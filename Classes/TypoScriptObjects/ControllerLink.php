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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * This is a WORKAROUND which evaluates all values of the "arguments" property through
 * TypoScript Processors and Eel Expressions; so one can write:
 *
 * 10 = TYPO3.Expose:RecordList.ControllerLink
 * 10.label = 'New'
 * 10.feature = 'TYPO3\\Expose\\Controller\\NewController'
 * # THIS IS THE IMPORTANT LINE:
 * 10.arguments.type = ${type}
 */
class ControllerLink extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {

	/**
	 * @return string
	 */
	public function evaluate() {
		if (isset($this->variables['arguments'])) {
			$this->recursivelyEvaluateProcessor($this->variables['arguments'], 'arguments');
		}

		return parent::evaluate();
	}

	/**
	 * @param array $array
	 * @param string $namespace
	 */
	protected function recursivelyEvaluateProcessor(array &$array, $namespace) {
		foreach ($array as $key => $value) {
			$array[$key] = $this->tsRuntime->evaluateProcessor($namespace . '.' . $key, $this, $value);
			if (is_array($array[$key])) {
				$this->recursivelyEvaluateProcessor($array[$key], $namespace . '.' . $key);
			}
		}
	}
}

?>