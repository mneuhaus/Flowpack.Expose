<?php
namespace TYPO3\Expose\FormElements;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A generic form element
 */
class InlineFormElement extends \TYPO3\Form\FormElements\Section {

	/**
	 * @var object
	 */
	protected $annotations;

	/**
	 * @var boolean
	 */
	protected $multipleMode = FALSE;

	/**
	 * @var string
	 */
	protected $namespace;

	/**
	 * @return array
	 */
	public function getAnnotations() {
		return $this->annotations;
	}

	/**
	 * @param object $annotations
	 * @return void
	 */
	public function setAnnotations($annotations) {
		$this->annotations = $annotations;
	}

	/**
	 * @return boolean
	 */
	public function getMultipleMode() {
		return $this->multipleMode;
	}

	/**
	 * @param boolean $mode
	 */
	public function setMultipleMode($mode) {
		$this->multipleMode = $mode;
	}

	/**
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * @param string $namespace
	 * @return void
	 */
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	/**
	 * @return integer
	 */
	public function getNextKey() {
		return count($this->getElements()) + 1;
	}

	/**
	 * @return array
	 */
	public function getPropertyNames() {
		$propertyNames = array();
		foreach ($this->getAnnotations()->getProperties() as $property) {
			$propertyNames[] = $property->getLabel();
		}

		return $propertyNames;
	}

	/**
	 * @return \TYPO3\Form\Core\Model\FormElementInterface
	 */
	public function getTemplate() {
		$containerSection = clone $this;
		$namespacedName = '_template.' . $this->namespace;
		$itemSection = $containerSection->createElement($namespacedName . '.000', $this->type . 'Item');
		$class = $this->annotations->getClass();
		$object = new $class();
		if (!isset($this->modelFormFactory->form) || !is_object($this->modelFormFactory->form)) {
			$this->modelFormFactory->form = $this->getRootForm();
		}
		$elements = $this->modelFormFactory->generateElements($object, $itemSection, $namespacedName . '.000');

		return $itemSection;
	}

	/**
	 * @return \TYPO3\Form\Core\Model\FormElementInterface
	 */
	public function getUnusedElement() {
		$containerSection = clone $this;
		$namespacedName = $this->namespace;
		$key = count($this->getElements());
		$itemSection = $containerSection->createElement(($namespacedName . '.') . $key, $this->type . 'Item');
		$class = $this->annotations->getClass();
		$object = new $class();
		if (!isset($this->modelFormFactory->form) || !is_object($this->modelFormFactory->form)) {
			$this->modelFormFactory->form = $this->getRootForm();
		}
		$elements = $this->modelFormFactory->generateElements($object, $itemSection, ($namespacedName . '.') . $key);

		return $itemSection;
	}
}

?>