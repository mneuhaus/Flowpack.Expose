<?php
namespace Flowpack\Expose\Core\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Reflection\PropertySchema;

/**
 */
abstract class AbstractOptionsProvider implements OptionsProviderInterface {

	/**
	 * @var PropertySchema
	 */
	protected $propertySchema;

	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * Syntax example:
	 *     array(
	 *         'someOptionName' => array(
	 * 				'default' => 'defaultValue',
	 * 				'description' => 'some description',
	 * 				'type' => '[string|integer|float|boolean|array]',
	 * 				'required' => [TRUE|FALSE]
	 * 			),
	 *         'someOtherOptionName' => array(
	 * 				'default' => 'defaultValue',
	 * 				'description' => 'some description',
	 * 				'type' => 'integer',
	 * 				'required' => FALSE
	 * 			),
	 *         ...
	 *     )
	 *
	 * @var array
	 */
	protected $supportedSettings = array();

	/**
	 * The configuration settings of this optionsProvider
	 * @see $supportedSettings
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 */
	public function __construct($propertySchema, $settings = array()) {
		$this->propertySchema = $propertySchema;

		unset($settings['Name']);

		// check for settings given but not supported
		if (($unsupportedSettings = array_diff_key($settings, $this->supportedSettings)) !== array()) {
			$supportedSettings = array_keys($this->supportedSettings);
			foreach (array_keys($unsupportedSettings) as $unsupportedSetting) {
				foreach ($supportedSettings as $supportedSetting) {
					$similarity = 0;
					similar_text($supportedSetting, $unsupportedSetting, $similarity);
					if ($similarity > 50) {
						throw new \TYPO3\Flow\Mvc\Exception(sprintf('The settings "%s" you\'re trying to set don\'t exist, did you mean: "%s" ?.', $unsupportedSetting, $supportedSetting), 1359625876);
					}
				}
			}
			throw new \TYPO3\Flow\Mvc\Exception(sprintf('The settings "%s" you\'re trying to set don\'t exist in class "%s".', implode(',', array_keys($unsupportedSettings)), get_class($this)), 1359625876);
		}

		// check for required settings being set
		array_walk(
			$this->supportedSettings,
			function($supportedSettingData, $supportedSettingName, $settings) {
				if (isset($supportedSettingData['required']) && $supportedSettingData['required'] == TRUE && !array_key_exists($supportedSettingName, $settings)) {
					throw new \TYPO3\Flow\Mvc\Exception('Missing required OptionsProvider setting: ' . $supportedSettingName . chr(10) . $supportedSettingData['description'], 1359625876);
				}
			},
			$settings
		);

		// merge with default values
		$this->settings = array_merge(
			array_map(
				function ($value) {
					return isset($value['default']) ? $value['default'] : NULL;
				},
				$this->supportedSettings
			),
			$settings
		);
	}

}