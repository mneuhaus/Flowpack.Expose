<?php
namespace TYPO3\Expose\TypoScriptObjects\Helpers;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 *
 */
class BaseFormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {

    public function build(array $configuration, $presetName) {
		$formDefaults = $this->getPresetConfiguration($presetName);

		return new \TYPO3\Form\Core\Model\FormDefinition($configuration['identifier'], $formDefaults);
	}
}
?>