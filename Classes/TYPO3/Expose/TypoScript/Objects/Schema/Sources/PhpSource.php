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
class PhpSource extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

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
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$schema = array();
		$schema['queryMethod'] = 'createQuery';
		$propertyNames = $this->reflectionService->getClassPropertyNames($this->getClassName());
		foreach ($propertyNames as $key => $propertyName) {
			$schema['properties'][$propertyName] = $this->getPropertyTypes($propertyName);
			$schema['properties'][$propertyName]['@position'] = ( $key + 1 ) * 100;
			$schema['properties'][$propertyName]['annotations'] = $this->reflectionService->getPropertyAnnotations($this->getClassName(), $propertyName);
		}
		return $schema;
	}

	public function getPropertyTypes($propertyName) {
		$vars = $this->reflectionService->getPropertyTagValues($this->getClassName(), $propertyName, 'var');

		if (strpos($vars[0], '<') !== FALSE) {
			preg_match('/([^<]+)<(.+)>/', $vars[0], $matches);
			$types = array(
				'type' => $matches[1],
				'elementType' => $matches[2]
			);
		} else {
			$types = array(
				'type' => $vars[0],
				'elementType' => NULL
			);
		}

		return $types;
	}
}

?>