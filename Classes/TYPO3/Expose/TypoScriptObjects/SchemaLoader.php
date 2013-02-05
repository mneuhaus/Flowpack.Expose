<?php
namespace TYPO3\Expose\TypoScriptObjects;

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
		return $this->sortNestedTypoScriptKeys();
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
		$cache = $this->cacheManager->getCache('TYPO3_Expose_SchemaCache');
		$identifier = sha1($this->getClassName()) . sha1($this->path);

		if (!$cache->has($identifier)) {
			$cache->set($identifier, $this->compileSchema());
		}

		return $cache->get($identifier);
	}

	public function compileSchema() {
		$schema = array();
		foreach ($this->getSources() as $sourceKey) {
			$source = $this->tsRuntime->render($this->path . '/sources/' . $sourceKey);
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

		$schema['properties'] = $this->sortArrayByPosition($schema['properties']);

		return $schema;
	}

	public function sortArrayByPosition($unsortedArray) {
		$arrayKeysWithPosition = array();

		foreach ($unsortedArray as $key => $subElement) {
			if (isset($subElement['@position'])) {
				$arrayKeysWithPosition[$key] = $subElement['@position'];
			} else {
				if (is_numeric($key)) {
					$arrayKeysWithPosition[$key] = $key;
				} else {
					$arrayKeysWithPosition[$key] = 0;
				}
			}
		}

			// $startKeys, $middleKeys and $endKeys are multi-dimensional arrays:
			// the KEY of each array is a PRIORITY, the VALUE is an array of sub-TypoScript-Object KEYS
		$startKeys = array();
		$middleKeys = array();
		$endKeys = array();
			// $beforeKeys and $afterKeys are multi-dimensional arrays
			// the key of each array is another KEY, the VALUE is an array of PRIORITIES of sub-TypoScript-Object KEYS
		$beforeKeys = array();
		$afterKeys = array();

			// First, we parse the positional string and depending on this string, add the elements to the three arrays from above
		foreach ($arrayKeysWithPosition as $key => $position) {
			$matches = array();
			if (preg_match('/^start(?: ([0-9s]+))?$/', $position, $matches)) {
				if (isset($matches[1])) {
					$startKeys[intval($matches[1])][] = $key;
				} else {
					$startKeys[0][] = $key;
				}
			} elseif (preg_match('/^end(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[1])) {
					$endKeys[intval($matches[1])][] = $key;
				} else {
					$endKeys[0][] = $key;
				}
			} elseif (preg_match('/^before ([a-zA-Z0-9]+)(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[2])) {
					$beforeKeys[$matches[1]][$matches[2]][] = $key;
				} else {
					$beforeKeys[$matches[1]][0][] = $key;
				}
			} elseif (preg_match('/^after ([a-zA-Z0-9]+)(?: ([0-9]+))?$/', $position, $matches)) {
				if (isset($matches[2])) {
					$afterKeys[$matches[1]][$matches[2]][] = $key;
				} else {
					$afterKeys[$matches[1]][0][] = $key;
				}
			} elseif (preg_match('/^[0-9]+$/', (string)$position)) {
				$middleKeys[intval($position)][] = $key;
			} else {
				throw new \TYPO3\TypoScript\Exception('The positional string "' . $position . '" is not supported.', 1345126502);
			}
		}

			// Now, sort the three arrays by priority key
		krsort($startKeys, SORT_NUMERIC);
		foreach ($beforeKeys as $key => &$keysByPriority) {
			krsort($keysByPriority, SORT_NUMERIC);
		}
		ksort($middleKeys, SORT_NUMERIC);
		foreach ($afterKeys as $key => &$keysByPriority) {
			ksort($keysByPriority, SORT_NUMERIC);
		}
		ksort($endKeys, SORT_NUMERIC);

			// Finally, collect all results and flatten them
		$prefinalKeys = array();
		$flattenerFunction = function($value, $key, $step) use (&$prefinalKeys, &$beforeKeys, &$afterKeys, &$flattenerFunction) {
			if (isset($beforeKeys[$value])) {
				array_walk_recursive($beforeKeys[$value], $flattenerFunction, $step);
				unset($beforeKeys[$value]);
			}
			$prefinalKeys[$step][] = $value;
			if (isset($afterKeys[$value])) {
				array_walk_recursive($afterKeys[$value], $flattenerFunction, $step);
				unset($afterKeys[$value]);
			}
		};

			// 1st step: collect regular keys and process before / after if keys occcured
		array_walk_recursive($startKeys, $flattenerFunction, 0);
		array_walk_recursive($middleKeys, $flattenerFunction, 2);
		array_walk_recursive($endKeys, $flattenerFunction, 4);

			// 2nd step: process before / after leftovers for unmatched keys
		array_walk_recursive($beforeKeys, $flattenerFunction, 1);
		array_walk_recursive($afterKeys, $flattenerFunction, 3);

		ksort($prefinalKeys);

			// 3rd step: mix everything together
		$finalKeys = array();
		array_walk_recursive($prefinalKeys, function($value) use (&$finalKeys) {
			$finalKeys[] = $value;
		});

		$sortedArray = array();
		foreach ($finalKeys as $key) {
			$sortedArray[$key] = $unsortedArray[$key];
		}

		return $sortedArray;
	}

	/**
	 * Collect the array keys inside $this->subElements with each position meta-argument.
	 *
	 * If there is no position but the array is numerically ordered, we use the array index as position.
	 *
	 * @return array an associative array where each key of $this->subElements has a position string assigned
	 */
	protected function collectArrayKeysAndPositions() {
		$arrayKeysWithPosition = array();

		foreach ($this->tsValue('sources') as $key => $subElement) {
			if (isset($subElement['__meta']['position'])) {
				$arrayKeysWithPosition[$key] = $subElement['__meta']['position'];
			} else {
				if (is_numeric($key)) {
					$arrayKeysWithPosition[$key] = $key;
				} else {
					$arrayKeysWithPosition[$key] = 0;
				}
			}
		}

		return $arrayKeysWithPosition;
	}

}

?>