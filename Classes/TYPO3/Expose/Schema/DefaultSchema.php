<?php
namespace TYPO3\Expose\Schema;


/*                                                                        *
 * This script belongs to the FLow framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class DefaultSchema {
	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\Kickstart\Utility\Inflector
	 * @Flow\Inject
	 */
	protected $inflector;

	/**
	 * @param string $className
	 * @param string $propertyPrefix
	 * @param \TYPO3\Flow\Reflection\ReflectionService $reflectionService
	 * @param \TYPO3\Kickstart\Utility\Inflector $inflector
	 */
	public function __construct($className, $propertyPrefix = '', \TYPO3\Flow\Reflection\ReflectionService $reflectionService, \TYPO3\Kickstart\Utility\Inflector $inflector) {
		$this->className = $className;
		$this->reflectionService = $reflectionService;
		$this->inflector = $inflector;

		$propertyNames = $this->reflectionService->getClassPropertyNames($this->className);
		foreach ($propertyNames as $key => $propertyName) {
			$property = $this->getPropertyTypes($propertyName);
			$property['name'] = $propertyPrefix . $propertyName;
			$property['label'] = $this->getPropertyLabel($propertyName, $property);
			$property['annotations'] = $this->reflectionService->getPropertyAnnotations($this->className, $propertyName);

			if ($property['metaType'] == 'SingleSelect' || $property['metaType'] == 'MultiSelect') {
				#$property['optionsProvider'] = $this->getOptionsProvider($property);
			}

			$this->properties[$propertyName] = $property;
		}
	}

	public function getProperty($propertyName) {
		if (stristr($propertyName, '.')) {
			$parts = explode('.', $propertyName);
			$propertyPrefix = array_shift($parts);
			$property = $this->getProperty($propertyPrefix);
			if ($property['metaType'] == 'MultiSelect') {
				$propertyPrefix.= '.' . array_shift($parts);
				$propertyClassSchema = new DefaultSchema($property['elementType'], $propertyPrefix . '.');
			} else {
				$propertyClassSchema = new DefaultSchema($property['type'], $propertyPrefix . '.');
			}

			return $propertyClassSchema->getProperty(implode('.', $parts));
		}
		return $this->properties[$propertyName];
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

		$types['metaType'] = $this->getMetaType($types);

		return $types;
	}

	public function getMetaType($property) {
		if (class_exists($property['type'])) {
			if ($this->reflectionService->isClassAnnotatedWith($property['type'], '\TYPO3\Flow\Annotations\Entity')) {
				return 'SingleSelect';
			}
		}

		if (($property['type'] === 'array' || $property['type'] === 'SplObjectStorage' || $property['type'] === '\Doctrine\Common\Collections\Collection' || $property['type'] === '\Doctrine\Common\Collections\ArrayCollection')) {
			return 'MultiSelect';
		}

		return $property['type'];
	}

	public function getOptionsProvider($property) {
		if (class_exists($property['type'])) {
			if ($this->reflectionService->isClassAnnotatedWith($property['type'], '\TYPO3\Flow\Annotations\Entity')) {
				return new \TYPO3\Expose\OptionsProvider\RelationOptionsProvider($property);
			}
		}

		if (($property['type'] === 'array' || $property['type'] === 'SplObjectStorage' || $property['type'] === '\Doctrine\Common\Collections\Collection' || $property['type'] === '\Doctrine\Common\Collections\ArrayCollection')) {
			return new \TYPO3\Expose\OptionsProvider\RelationOptionsProvider($property);
		}
	}

	public function getPropertyLabel($propertyName, $property) {
		return $this->inflector->humanizeCamelCase($propertyName, FALSE);
	}
}

?>