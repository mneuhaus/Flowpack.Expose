<?php
namespace Flowpack\Expose\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class SettingsValidator {

	public static function validate($settings, $supportDefinitions) {
		// check for settings given but not supported
		if (($unsupportedSettings = array_diff_key($settings, $supportDefinitions)) !== array()) {
			$supportedSettings = array_keys($supportDefinitions);
			foreach (array_keys($unsupportedSettings) as $unsupportedSetting) {
				foreach ($supportedSettings as $supportedSetting) {
					$similarity = 0;
					similar_text($supportedSetting, $unsupportedSetting, $similarity);
					if ($similarity > 50) {
						throw new \TYPO3\Flow\Mvc\Exception(sprintf('The settings "%s" you\'re trying to set don\'t exist, did you mean: "%s" ?.', $unsupportedSetting, $supportedSetting), 1407785248);
					}
				}
			}
			throw new \TYPO3\Flow\Mvc\Exception(sprintf('The settings "%s" you\'re trying to set don\'t exist.', implode(',', array_keys($unsupportedSettings))), 1407785250);
		}

		// check for required settings being set
		array_walk(
			$supportDefinitions,
			function($supportedSettingData, $supportedSettingName, $settings) {
				if (isset($supportedSettingData['required']) && $supportedSettingData['required'] == TRUE && !array_key_exists($supportedSettingName, $settings)) {
					throw new \TYPO3\Flow\Mvc\Exception('Missing required setting: ' . $supportedSettingName . chr(10) . $supportedSettingData['description'], 1407785252);
				}
			},
			$settings
		);
	}
}
?>