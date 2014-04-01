<?php
namespace TYPO3\Expose\TypoScript\Objects\FormElementBuilder;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Render a Form section using the Form framework
 */
class InlineFormElementBuilder extends DefaultFormElementBuilder {
	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * @return object
	 */
	public function getRequest() {
		return $this->bootstrap->getActiveRequestHandler()->getHttpRequest()->createActionRequest();
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 */
	public function evaluate() {
		$parentFormElement = $this->tsValue('parentFormElement');
		if (!($parentFormElement instanceof \TYPO3\Form\Core\Model\AbstractSection)) {
			throw new \Exception('TODO: parent form element must be a section-like element');
		}

		$schema = $this->getSchema($this->tsValue('className'));
		$propertySchema = $schema['properties'][$this->tsValue('propertyName')];

		$this->tsRuntime->pushContext('propertySchema', $propertySchema);
		if (isset($propertySchema['annotations']['Doctrine\ORM\Mapping\ManyToMany']) || isset($propertySchema['annotations']['Doctrine\ORM\Mapping\OneToMany'])) {
			$containerSection = $this->createMultipleForm($parentFormElement, $propertySchema);
		} else {
			$containerSection = $this->createSingleForm($parentFormElement, $propertySchema);
		}
		$this->tsRuntime->popContext();
		return $containerSection;
	}

	/**
	 * @param \TYPO3\Form\Core\Model\AbstractSection $parentFormElement
	 * @param string $propertySchema
	 */
	public function createMultipleForm($parentFormElement, $propertySchema) {
		$namespace = $this->tsValue('identifier');
		$className = $propertySchema['elementType'];
		$objects = $this->tsValue('propertyValue');
		$schema = $this->getSchema($className);

		if (is_null($objects) || count($objects) < 1) {
			$objects = array();
		}

		$objects = $this->createObjectsForRequestIfNeeded($objects, $schema, $className);

		$containerSection = $this->createContainerSection(
			$parentFormElement,
			$this->tsValue('identifier'),
			$this->tsValue('formFieldType'),
			$className,
			$propertySchema
		);
		$containerSection->setDataType('Doctrine\Common\Collections\Collection<' . $className . '>');
		$containerSection->setCounter(count($objects));

		foreach ($objects as $key => $object) {
			$itemSection = $containerSection->createElement($namespace . '.' . $key, $this->tsValue('formFieldType') . 'Item');
			$itemSection->setFormBuilder($this->tsValue('formBuilder'));
			$section = $this->tsValue('formBuilder')->createFormForSingleObject($itemSection, $object, $namespace . '.' . $key);
			$section->setDataType($className);
		}

		return $containerSection;
	}

	/**
	 * @param \TYPO3\Form\Core\Model\AbstractSection $parentFormElement
	 * @param string $propertySchema
	 */
	public function createSingleForm($parentFormElement, $propertySchema) {
		$namespace = $this->tsValue('identifier');
		$className = $propertySchema['type'];
		$object = $this->tsValue('propertyValue');
		$schema = $this->getSchema($className);

		if (is_null($object)) {
			$object = $this->createNewObject($className, $schema);
		}

		$containerSection = $this->createContainerSection(
			$parentFormElement,
			'c.' . $this->tsValue('identifier'),
			$this->tsValue('formFieldType'),
			$className,
			$propertySchema
		);

		$itemSection = $containerSection->createElement($namespace, $this->tsValue('formFieldType') . 'Item');
		$itemSection->setFormBuilder($this->tsValue('formBuilder'));
		$itemSection->setDataType($className);
		$this->tsValue('formBuilder')->createFormForSingleObject($itemSection, $object, $namespace);

		return $containerSection;
	}

	public function createContainerSection($parentFormElement, $namespace, $fieldType, $className, $propertySchema) {
		$containerSection = $parentFormElement->createElement($namespace, $fieldType);
		$containerSection->setFormBuilder($this->tsValue('formBuilder'));
		$containerSection->setPropertySchema($propertySchema);
		$containerSection->setClass($className);
		if (!(isset($propertySchema['noLabel']) && $propertySchema['noLabel'] === TRUE)) {
			$containerSection->setLabel($propertySchema['label']);
		}
		return $containerSection;
	}

	/**
	 * @param string $schema
	 */
	public function createObjectsForRequestIfNeeded($objects, $schema, $type) {
		$namespace = $this->tsValue('identifier');
		$requestArguments = $this->getRequest()->getMainRequest()->getPluginArguments();
		if (isset($requestArguments['form'])) {
			// Form has been submitted
			$formArguments = \TYPO3\Flow\Utility\Arrays::getValueByPath($requestArguments['form'], $namespace);
			if (is_array($formArguments)) {
				// the form contains data for this namespace
				$newObjects = array();
				foreach ($formArguments as $key => $value) {
					if (isset($value['__identity']) === TRUE) {
						// there is already an entity, so skip
						continue;
					}
					if (isset($objects[$key]) && $this->persistenceManager->isNewObject($objects[$key])) {
						$newObjects[$key] = $objects[$key];
					} else {
						// this is a new object and might need defaults by __construct
						$newObjects[$key] = $this->createNewObject($type, $schema);
					}
				}
				if (count($newObjects) > 0) {
					$objects = $newObjects;
				}
			} else {
				// this namespace is emptied
				$objects = array();
			}
		}
		return $objects;
	}

	public function getSchema($className) {
		$this->tsRuntime->pushContext('className', $className);
		$schema = $this->tsRuntime->render($this->path . '/<TYPO3.Expose:SchemaLoader>');
		$this->tsRuntime->popContext();
		return $schema;
	}

	public function createNewObject($className, $schema) {
		if (isset($schema['factory'])) {
			$factoryClassName = $schema['factory'];
			return new $factoryClassName($schema);
		}
		return new $className();
	}
}
?>