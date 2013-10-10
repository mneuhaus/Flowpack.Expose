<?php
namespace TYPO3\Expose\TypoScript\Objects\Schema\Sources;

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
class NodeSource extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * the object to build the form for
	 *
	 * @var object
	 */
	protected $object;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

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

	/**
	 * @param object $object
	 */
	public function setObject($object) {
		$this->object = $object;
	}

	/**
	 * @return object
	 */
	public function getObject() {
		return $this->tsValue('object');
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		if (class_exists('\TYPO3\TYPO3CR\Domain\Service\ContentTypeManager')) {
			$object = $this->getObject();
			$schema = $object->getContentType()->getConfiguration();
			$i = 1;
			foreach ($schema['properties'] as $key => $value) {
				if (substr($key, 0, 1) == '_') {
					unset($schema['properties'][$key]);
					$key = substr($key, 1);
					$schema['properties'][$key] = $value;
				}
				$schema['properties'][$key]['@position'] = ( $i ) * 100;
				$i++;
			}
			return $schema;
		}
		return array();
	}
}

?>