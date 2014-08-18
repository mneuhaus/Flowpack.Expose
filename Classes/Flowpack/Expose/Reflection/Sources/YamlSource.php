<?php
namespace Flowpack\Expose\Reflection\Sources;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\Sources\AbstractSchemaSource;
use Flowpack\Expose\Utility\SettingsValidator;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Utility\Arrays;

/**
 */
class YamlSource extends AbstractSchemaSource {

	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedClassSettings = array(
		'listProperties' => array(
			'description' => 'Contains properties that should be display as columns in the list view'
		),
		'searchProperties' => array(
			'description' => 'List of properties used by the SearchBehavior'
		),
		'filterProperties' => array(
			'description' => 'List of properties used by the FilterBehavior'
		),
		'defaultSortBy' => array(
			'description' => 'Default property to sort by'
		),
		'defaultOrder' => array(
			'description' => 'Default order to sort the property'
		),
		'defaultWrap' => array(
			'description' => 'Default wrap to use for the form controls'
		),
		'layout' => array(
			'description' => 'Layout used by the Crud Controller'
		),
		'listBehaviors' => array(
			'description' => 'Array of Behaviors that are used by the list view.'
		),
		'properties' => array(
			'description' => 'Contains settings for properties of this class'
		)
	);

	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedPropertySettings = array(
		'label' => array(
			'description' => 'Description that will be places under the form control'
		),
		'control' => array(
			'description' => 'Contains properties that should be display as columns in the list view'
		),
		'handler' => array(
			'description' => 'Contains a handler that is invoked during the form submission process to validate + modify submitted data'
		),
		'infotext' => array(
			'description' => 'Description that will be places under the form control'
		),
		'optionsProvider' => array(
			'description' => 'Description that will be places under the form control'
		),
		'wrap' => array(
			'description' => 'Description that will be places under the form control'
		)
	);

	/**
	 * @Flow\Inject
	 * @var ConfigurationManager
	 */
	protected $configurationManager;

	public function compileSchema() {
		$schema = (array) $this->configurationManager->getConfiguration('Expose', $this->className);
		SettingsValidator::validate($schema, $this->supportedClassSettings);

		if (isset($schema['properties'])) {
			foreach ($schema['properties'] as $propertyName => $propertySettings) {
				SettingsValidator::validate($propertySettings, $this->supportedPropertySettings);
			}
		}

		$arrayKeys = array(
			'listProperties',
			'searchProperties',
			'filterProperties'
		);
		foreach ($arrayKeys as $key) {
			if (isset($schema[$key]) && is_string($schema[$key])) {
				$schema[$key] = Arrays::trimExplode(',', $schema[$key]);
			}
		}
		return $schema;
	}
}

?>