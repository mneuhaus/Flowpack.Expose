<?php
namespace TYPO3\Expose\TypoScript\Objects;

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
use TYPO3\Flow\Utility\Exception\InvalidPositionException;
use TYPO3\Flow\Utility\PositionalArraySorter;

/**
 * Render a Form section using the Form framework
 */
class SchemaLoader extends \TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation {
	/**
	 * @var TYPO3\Flow\Cache\CacheManager
	 * @Flow\Inject
	 */
	protected $cacheManager;

	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 *
	 * @var array
	 */
	protected $sources;

	/**
	 *
	 * @var array
	 */
	protected $propertyCases;

	/**
	 * @param string $className
	 * @return void
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	public function getClassName() {
		return $this->tsValue('className');
	}

	public function setSources($sources) {
		$this->sources = $sources;
	}

	public function getSources() {
		return $this->tsValue('sources');
	}

	/**
	 * @param array $propertyCases
	 */
	public function setPropertyCases($propertyCases) {
		$this->propertyCases = $propertyCases;
	}

	/**
	 * @return array
	 */
	public function getPropertyCases() {
		return $this->tsValue('propertyCases');
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		\TYPO3\Expose\Utility\StringRepresentation::setTypoScriptRuntime($this->tsRuntime);

		$cache = $this->cacheManager->getCache('TYPO3_Expose_SchemaCache');
		$identifier = sha1($this->getClassName()) . sha1($this->path);

		if (!$cache->has($identifier)) {
			$cache->set($identifier, $this->compileSchema());
		}

		return $cache->get($identifier);
	}

	public function compileSchema() {
		$schema = array();
		foreach ($this->getSources() as $key => $source) {
			$schema = \TYPO3\Flow\Utility\Arrays::arrayMergeRecursiveOverrule($schema, $source);
		}


		foreach ($schema['properties'] as $propertyName => $propertySchema) {
			$this->tsRuntime->pushContext('schema', $schema);
			$this->tsRuntime->pushContext('propertySchema', $propertySchema);
			$this->tsRuntime->pushContext('propertyName', $propertyName);
			foreach (array_keys($this->getPropertyCases()) as $propertyCase) {
				$result = $this->tsRuntime->render($this->path . '/propertyCases/' . $propertyCase);
				$schema['properties'][$propertyName][$propertyCase] = $result;
			}
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
		}

		$arraySorter = new PositionalArraySorter($schema['properties'], '@position');
		try {
			$schema['properties'] = $arraySorter->toArray();
		} catch (InvalidPositionException $exception) {
			throw new TypoScript\Exception('Invalid position string', 1345126502, $exception);
		}

		return $schema;
	}
}

?>