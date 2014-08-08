<?php
namespace Flowpack\Expose\Reflection;

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
use TYPO3\Flow\Cache\CacheManager;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Utility\Exception\InvalidPositionException;
use TYPO3\Flow\Utility\PositionalArraySorter;

/**
 * Render a Form section using the Form framework
 */
class ClassSchema {

	/**
	 * @param ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * @var array
	 */
	protected $schema;

	/**
	 * @var array
	 */
	protected $properties;

	/**
	 * @var string
	 */
	protected $propertyPrefix;

	/**
	 *
	 * @param string $className
	 * @param strign $propertyPrefix
	 * @param strign $scope
	 * @param CacheManager $cacheManager
	 * @param ConfigurationManager $configurationManager
	 * @return void
	 */
	public function __construct($className, $propertyPrefix = NULL, $scope = NULL, CacheManager $cacheManager, ConfigurationManager $configurationManager) {
		$this->className = $className;
		$this->propertyPrefix = $propertyPrefix;

		$cache = $cacheManager->getCache('Flowpack_Expose_SchemaCache');
		$identifier = md5($className . $scope);

		if (!$cache->has($identifier)) {
			$this->configurationManager = $configurationManager;
			$cache->set($identifier, $this->compileSchema());
		}

		$this->schema = $cache->get($identifier);
		$this->properties = $this->schema['properties'];
	}

	public function getPropertyNames() {
		return array_keys($this->properties);
	}

	public function getProperties($properyNames = NULL) {
		if ($properyNames === NULL) {
			$properyNames = $this->getPropertyNames;
		}

		$properties = array();
		foreach ($properyNames as $propertyName) {
			if ($propertyName === '__toString') {
				$property = new PropertySchema(array(
					'name' => $propertyName,
					'label' => ''
				), $this, $this->propertyPrefix);
			} else {
				$property = $this->getProperty($propertyName);
			}
			$properties[$propertyName] = $property;
		}

		return $properties;
	}

	public function getClassName() {
		return $this->className;
	}

	public function getProperty($propertyName) {
		if (stristr($propertyName, '.')) {
			$parts = explode('.', $propertyName);
			$propertyPrefix = array_shift($parts);
			$property = new PropertySchema($this->properties[$propertyPrefix], $this, $this->propertyPrefix);
			if ($property->getElementType() !== NULL) {
				$propertyPrefix.= '.' . array_shift($parts);
				$propertyClassSchema = new ClassSchema($property->getElementType(), $propertyPrefix);
			} else {
				$propertyClassSchema = new ClassSchema($property->getType(), $propertyPrefix);
			}
			return $propertyClassSchema->getProperty(implode('.', $parts));
		}

		return new PropertySchema($this->properties[$propertyName], $this, $this->propertyPrefix);
	}

	public function getListPropertyNames() {
		return $this->schema['listProperties'];
	}

	public function getListProperties() {
		return $this->getProperties($this->getListPropertyNames());
	}

	public function getSearchPropertyNames() {
		return $this->schema['searchProperties'];
	}

	public function getSearchProperties() {
		return $this->getProperties($this->getSearchPropertyNames());
	}

	public function getFilterPropertNames() {
		return $this->schema['filterProperties'];
	}

	public function getFilterProperties() {
		return $this->getProperties($this->getFilterPropertNames());
	}

	public function getDefaultOrder() {
		return $this->schema['defaultOrder'];
	}

	public function getDefaultSortBy() {
		return $this->schema['defaultOrder'];
	}

	public function getListProcessors() {
		return $this->schema['listProcessors'];
	}

	public function getSources() {
		$sources = $this->configurationManager->getConfiguration('Settings', 'Flowpack.Expose.ClassSchemaSources');
		foreach ($sources as $key => $sourceClassName) {
			$sources[$key] = new $sourceClassName($this->className);
		}
		return $sources;
	}

	public function compileSchema() {
		$schema = array(
			'properties' => array()
		);

		foreach ($this->getSources() as $key => $source) {
			$schema = \TYPO3\Flow\Utility\Arrays::arrayMergeRecursiveOverrule($schema, $source->compileSchema());
		}

		// $package = $this->getPackageByClassName($this->getClassName());
		// $translatables = array('label', 'infotext', 'confirmationLabel');

		// foreach ($schema['properties'] as $propertyName => $propertySchema) {
		// 	foreach ($propertySchema as $key => $value) {
		// 		if (in_array($key, $translatables) && empty($value) === FALSE) {
		// 			$id = str_replace('\\', '.', ltrim($this->getClassName(), '\\')) . '.' . $propertyName . '.' . $key;
		// 			$originalLabel = $schema['properties'][$propertyName][$key];
		// 			$translation = $this->translator->reset()
		// 				->setSourceName('Expose')
		// 				->setPackageKey($package)
		// 				->translate($id, $originalLabel);
		// 			if ($translation !== $originalLabel) {
		// 				$schema['properties'][$propertyName][$key] = $translation;
		// 			}
		// 		}
		// 	}
		// }

		$arraySorter = new PositionalArraySorter($schema['properties'], 'position');
		try {
			$schema['properties'] = $arraySorter->toArray();
		} catch (InvalidPositionException $exception) {
			throw new TypoScript\Exception('Invalid position string', 1345126502, $exception);
		}

		return $schema;
	}
}

?>