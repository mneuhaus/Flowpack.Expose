<?php
namespace TYPO3\Expose\FormElements;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A generic form element
 */
class InlineFormElement extends \TYPO3\Form\FormElements\Section {

	/**
	 *
	 * @var string
	 */
	protected $class;

	/**
	 *
	 * @var string
	 */
	protected $counter = 0;

	/**
	 *
	 * @var object
	 */
	protected $formBuilder;

	/**
	 * Default Value of this Section
	 *
	 * @var mixed
	 */
	protected $defaultValue = NULL;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $propertySchema;

	/**
	 * Check if the this inline element handles a multiple relation
	 *
	 * @return boolean
	 */
	public function isMultipleMode() {
		if (isset($this->propertySchema['inline']['multipleMode'])) {
			return $this->propertySchema['inline']['multipleMode'];
		}

		if (isset($this->propertySchema['annotations']['Doctrine\ORM\Mapping\ManyToMany']) || isset($this->propertySchema['annotations']['Doctrine\ORM\Mapping\OneToMany'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Set the formBuilder for later use
	 *
	 * @param object $formBuilder
	 */
	public function setFormBuilder($formBuilder) {
		$this->formBuilder = $formBuilder;
	}

	/**
	 * Return a key for the next unused element
	 *
	 * @return integer
	 */
	public function getNextKey() {
		return $this->counter + 1;
	}

	/**
	 * Set the default value with which the Form Element should be initialized
	 * during display.
	 *
	 * @param mixed $defaultValue the default value for this Form Element
	 * @api
	 */
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}

	/**
	 * Set the current class
	 *
	 * @param string $class
	 */
	public function setClass($class) {
		$this->class = $class;
	}

	/**
	 * Get the current class
	 *
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * Set the current counter
	 * @param integer $counter
	 */
	public function setCounter($counter) {
		$this->counter = $counter;
	}

	public function setPropertySchema($propertySchema) {
		$this->propertySchema = $propertySchema;
	}

	public function getPropertySchema() {
		return $this->propertySchema;
	}

	/**
	 * Returns the propertNames for the elementType
	 *
	 * @return array
	 */
	public function getPropertyNames() {
		$class = $this->getClass();
		$propertyNames = $this->reflectionService->getClassPropertyNames($class);
		return $propertyNames;
	}

	/**
	 * Returns the schema for the target element type
	 *
	 * @return array
	 */
	public function getElementSchema() {
		$class = $this->getClass();
		$schema = $this->formBuilder->getSchema(new $class());
		return $schema;
	}

	/**
	 * Returns a container section with elements as template for inline editing
	 *
	 * @return array
	 */
	public function getTemplate() {
		$class = $this->getClass();
		$object = new $class();
		$namespace = '_template.' . $this->getIdentifier() . '.000';
		$parentSection = clone $this;
		$inlineElement = $this->propertySchema['inline']['element'];
		$containerSection = $parentSection->createElement($namespace, $inlineElement . 'Item');
		$section = $this->formBuilder->createFormForSingleObject($containerSection, $object, $namespace);
		return $containerSection;
	}

	/**
	 * Returns an section with new unused elements for inline editing
	 *
	 * @return array
	 */
	public function getUnusedElement() {
		$class = $this->getClass();
		$object = new $class();
		$namespace = $this->getIdentifier();
		if ($this->isMultipleMode()) {
			$namespace .= '.' . $this->counter;
		}
		$inlineElement = $this->propertySchema['inline']['element'];
		$containerSection = $this->createElement($namespace, $inlineElement . 'Item');
		$containerSection->setPropertySchema($this->propertySchema);
		$section = $this->formBuilder->createElementsForSection(count($this->renderables), $containerSection, $namespace, $object);
		return $containerSection;
	}

	public function getElements() {
		$elements = array();
		foreach ($this->renderables as $element) {
			$parts = explode('.', $element->getIdentifier());
			$elements[array_pop($parts)] = $element;
		}
		return $elements;
	}
}

?>