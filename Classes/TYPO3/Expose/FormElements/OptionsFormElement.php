<?php
namespace TYPO3\Expose\FormElements;

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
 * A generic form element
 */
class OptionsFormElement extends ComplexFormElement {
	/**
	 * Default OptionsProvider
	 *
	 * @var string
	 **/
	protected $defaultOptionsProvider = 'TYPO3\Expose\OptionsProvider\RelationOptionsProvider';

	/**
	 * @return \TYPO3\Expose\Core\OptionsProvider\OptionsProviderInterface
	 */
	public function getOptionsProvider() {
		$optionsProviderClass = $this->defaultOptionsProvider;
		$optionsProvider = new $optionsProviderClass();

		return $optionsProvider;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		$this->properties['options'] = $this->getOptionsProvider()->getOptions();

		return $this->properties;
	}
}

?>