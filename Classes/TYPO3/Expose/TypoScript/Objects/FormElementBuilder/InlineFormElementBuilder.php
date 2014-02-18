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

use TYPO3\Flow\Reflection\ObjectAccess;

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
		$namespace = $this->tsValue('identifier');
		if (isset($propertySchema['annotations']['Doctrine\ORM\Mapping\ManyToMany']) || isset($propertySchema['annotations']['Doctrine\ORM\Mapping\OneToMany'])) {
			$className = $propertySchema['elementType'];
			$objects = $this->tsValue('propertyValue');

			if (is_null($objects) || count($objects) < 1) {
				$objects = array();
			}

			$requestArguments = $this->getRequest()->getMainRequest()->getPluginArguments();
			if (isset($requestArguments['form'])) {
				$formArguments = \TYPO3\Flow\Utility\Arrays::getValueByPath($requestArguments['form'], $namespace);
				if (is_array($formArguments)) {
					$newObjects = array();
					foreach ($formArguments as $key => $value) {
						if (isset($value['__identity']) === FALSE) {
							$newObjects[$key] = new $className();
						}
					}
					if (count($newObjects)) {
						$objects = $newObjects;
					}
				} else {
					$objects = array();
				}
			}

			$containerSection = $parentFormElement->createElement($this->tsValue('identifier'), $this->tsValue('formFieldType'));
			$containerSection->setFormBuilder($this->tsValue('formBuilder'));
			$containerSection->setClass($className);
			$containerSection->setLabel($propertySchema['label']);
			$containerSection->setDataType('Doctrine\Common\Collections\Collection<' . $className . '>');
			$containerSection->setCounter(count($objects));
			$containerSection->setPropertySchema($propertySchema);

			foreach ($objects as $key => $object) {
				$itemSection = $containerSection->createElement($namespace . '.' . $key, $this->tsValue('formFieldType') . 'Item');
				$itemSection->setFormBuilder($this->tsValue('formBuilder'));
				$section = $this->tsValue('formBuilder')->createFormForSingleObject($itemSection, $object, $namespace . '.' . $key);
				$section->setDataType($className);
			}

		} else {
			$className = $propertySchema['type'];
			$object = $this->tsValue('propertyValue');

			if (is_null($object)) {
				$object = new $className();
			}

			$containerSection = $parentFormElement->createElement('c.' . $this->tsValue('identifier'), $this->tsValue('formFieldType'));
			$containerSection->setFormBuilder($this->tsValue('formBuilder'));
			$containerSection->setLabel($propertySchema['label']);
			$containerSection->setClass($className);
			$containerSection->setPropertySchema($propertySchema);

			$itemSection = $containerSection->createElement($namespace, $this->tsValue('formFieldType') . 'Item');
			$itemSection->setFormBuilder($this->tsValue('formBuilder'));
			$itemSection->setDataType($className);
			$section = $this->tsValue('formBuilder')->createFormForSingleObject($itemSection, $object, $namespace);
		}
		$this->tsRuntime->popContext();
		return $containerSection;
	}

	public function getSchema($className) {
		$this->tsRuntime->pushContext('className', $className);
		$schema = $this->tsRuntime->render($this->path . '/<TYPO3.Expose:SchemaLoader>');
		$this->tsRuntime->popContext();
		return $schema;
	}
}
?>