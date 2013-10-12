<?php
namespace TYPO3\Expose\TypoScript\Objects\Helpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class BaseFormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {
	public function setTsRuntime($tsRuntime) {
		$this->tsRuntime = $tsRuntime;
	}

	/**
	 * @param array $configuration
	 * @param string $presetName
	 * @return \TYPO3\Form\Core\Model\FormDefinition
	 */
	public function build(array $configuration, $presetName) {
		$formDefaults = $this->getPresetConfiguration($presetName);

		$formElementTypes = array();
		$namespaces = $this->tsRuntime->evaluate('<TYPO3.Form:Namespaces>');
		foreach ($namespaces as $namespace) {
			$fields = $this->tsRuntime->evaluate('<' . $namespace . '>');
			$parts = explode(':', $namespace);
			$namespace = $parts[0];
			foreach ($fields as $fieldName => $field) {
				foreach ($field as $key => $value) {
					if (is_array($value) && count($value) == 0) {
						unset($field[$key]);
					}
				}
				$formElementTypes[$namespace . ':' . $fieldName] = $field;
			}
		}
		$formDefaults['formElementTypes'] = \TYPO3\Flow\Utility\Arrays::arrayMergeRecursiveOverrule($formDefaults['formElementTypes'], $formElementTypes);

		return new \TYPO3\Expose\Form\FormDefinition($configuration['identifier'], $formDefaults);
	}

}

?>