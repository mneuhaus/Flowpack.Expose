<?php
namespace TYPO3\Expose\Form;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * Concrete configuration object for the PropertyMapper.
 *
 * @api
 */
class PropertyMappingConfiguration extends \TYPO3\Flow\Property\PropertyMappingConfiguration {

	/**
	 * If TRUE, unknown properties will be mapped.
	 *
	 * @var boolean
	 */
	protected $mapUnknownProperties = TRUE;

	public function __construct() {
		$this->setTypeConverterOption('TYPO3\\Flow\\Property\\TypeConverter\\PersistentObjectConverter', \TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
		$this->setTypeConverterOption('TYPO3\\Flow\\Property\\TypeConverter\\PersistentObjectConverter', \TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
		$this->allowAllProperties();
	}

	/**
	 * Returns the sub-configuration for the passed $propertyName. Must ALWAYS return a valid configuration object!
	 *
	 * @param string $propertyName
	 * @return \TYPO3\Flow\Property\PropertyMappingConfigurationInterface the property mapping configuration for the given $propertyName.
	 * @api
	 */
	public function getConfigurationFor($propertyName) {
		if (isset($this->subConfigurationForProperty[$propertyName])) {
			return $this->subConfigurationForProperty[$propertyName];
		} elseif (isset($this->subConfigurationForProperty[self::PROPERTY_PATH_PLACEHOLDER])) {
			return $this->subConfigurationForProperty[self::PROPERTY_PATH_PLACEHOLDER];
		}

		return new \TYPO3\Expose\Form\PropertyMappingConfiguration();
	}

}
?>