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

	/**
	 * @param array $configuration
	 * @param string $presetName
	 * @return \TYPO3\Form\Core\Model\FormDefinition
	 */
	public function build(array $configuration, $presetName) {
		$formDefaults = $this->getPresetConfiguration($presetName);

		return new \TYPO3\Expose\Form\FormDefinition($configuration['identifier'], $formDefaults);
	}

}

?>