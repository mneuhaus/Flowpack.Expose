<?php
namespace TYPO3\Expose\Form\Elements;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A generic form element
 */
class OptionsFormElement extends ComplexFormElement {
	/**
	 * Default OptionsProvider
	 *
	 * @var string
	 */
	protected $defaultOptionsProvider = 'TYPO3\Expose\OptionsProvider\RelationOptionsProvider';

	/**
	 * @var array
	 */
	protected $propertySchema;

	public function setPropertySchema($propertySchema) {
		$this->propertySchema = $propertySchema;
	}

	/**
	 * @return \TYPO3\Expose\Core\OptionsProvider\OptionsProviderInterface
	 */
	public function getOptionsProvider() {
		$optionsProviderClass = $this->defaultOptionsProvider;
		if (isset($this->propertySchema['optionsProvider']['class'])) {
			$optionsProviderClass = $this->propertySchema['optionsProvider']['class'];
			if (!stristr($optionsProviderClass, '\\')) {
				$optionsProviderClass = 'TYPO3\Expose\OptionsProvider\\' . $optionsProviderClass . 'OptionsProvider';
			}
		}
		$optionsProvider = new $optionsProviderClass($this->propertySchema);

		return $optionsProvider;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		$options = array('' => NULL);
		foreach ($this->getOptionsProvider()->getOptions() as $key => $value) {
			$options[$key] = $value;
		}
		$this->properties['options'] = $options;

		return $this->properties;
	}
}

?>