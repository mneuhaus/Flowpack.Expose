<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * OptionsProvider for related Beings
 *
 */
class ArrayOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

	/**
	 * This functions returns the Options defined by a internal property
	 * or Annotations
	 *
	 * @return array $options
	 */
	public function getOptions() {
		return $this->propertySchema['optionsProvider']['options'];
	}

}

?>