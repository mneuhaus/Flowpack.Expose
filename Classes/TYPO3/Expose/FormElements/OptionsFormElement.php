<?php
namespace TYPO3\Expose\FormElements;

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
 * A generic form element
 */
class OptionsFormElement extends ComplexFormElement {

	/**
	 * @return object
	 */
	public function getAnnotations() {
		return $this->properties['annotations'];
	}

	/**
	 * @return \TYPO3\Expose\Core\OptionsProvider\OptionsProviderInterface
	 */
	public function getOptionsProvider() {
		$optionsProviderClass = (string)$this->getAnnotations()->getOptionsProvider();
		$optionsProvider = new $optionsProviderClass($this->getAnnotations());

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