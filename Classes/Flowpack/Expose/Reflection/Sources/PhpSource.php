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
use TYPO3\Flow\Annotations as Flow;

/**
 */
class PhpSource extends AbstractSchemaSource {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @Flow\Inject(setting="Controls")
	 * @var array
	 */
	protected $controls;

	public function compileSchema() {
		$schema = array('properties' => array());
		$propertyNames = $this->reflectionService->getClassPropertyNames($this->className);
		foreach ($propertyNames as $key => $propertyName) {
			if ($this->propertyShouldBeIgnored($propertyName) === TRUE) {
				continue;
			}
			$propertySchema = $this->getPropertyTypes($propertyName);
			$propertySchema['control'] = $this->resolveControl($propertySchema);
			$propertySchema['isCollection'] = $this->isCollection($propertySchema);
			$schema['properties'][$propertyName] = $propertySchema;
		}
		return $schema;
	}

	public function getPropertyTypes($propertyName) {
		$vars = $this->reflectionService->getPropertyTagValues($this->className, $propertyName, 'var');

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

	public function resolveControl($property) {
		if (class_exists($property['type'])) {
			if ($this->reflectionService->isClassAnnotatedWith($property['type'], '\TYPO3\Flow\Annotations\Entity')) {
				return 'SingleSelect';
			}
		}

		if (($property['type'] === 'array' || $property['type'] === 'SplObjectStorage' || ltrim($property['type'], '\\') === 'Doctrine\Common\Collections\Collection' || ltrim($property['type'], '\\') === 'Doctrine\Common\Collections\ArrayCollection')) {
			return 'MultiSelect';
		}

		if (isset($this->controls[$property['type']])) {
			return $this->controls[$property['type']];
		}

		return $property['type'];
	}

	public function isCollection($property) {
		if (($property['type'] === 'array' || $property['type'] === 'SplObjectStorage' || $property['type'] === '\Doctrine\Common\Collections\Collection' || $property['type'] === '\Doctrine\Common\Collections\ArrayCollection')) {
			return TRUE;
		}

		return FALSE;
	}
}

?>