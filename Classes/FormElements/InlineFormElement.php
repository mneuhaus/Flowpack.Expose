<?php
namespace Foo\ContentManagement\FormElements;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A generic form element
 */
class InlineFormElement extends \TYPO3\Form\FormElements\Section {
	/**
	 *
	 * @var object
	 **/
	protected $annotations;

	/**
	 *
	 * @var boolean
	 **/
	protected $multipleMode = false;

	/**
	 *
	 * @var string
	 **/
	protected $namespace;

	/**
	 * @var \Foo\ContentManagement\Adapters\ContentManager
	 * @FLOW3\Inject
	 */
	protected $contentManager;

	/**
	 * @var \Foo\ContentManagement\Factory\ModelFormFactory
	 * @FLOW3\Inject
	 */
	protected $modelFormFactory;

	public function getPropertyNames() {
		$propertyNames = array();
		foreach ($this->getAnnotations()->getProperties() as $property) {
			$propertyNames[] = $property->getLabel();
		}
		return $propertyNames;
	}

	public function setAnnotations($annotations) {
		$this->annotations = $annotations;
	}

	public function getAnnotations() {
		return $this->annotations;
	}

	public function getMultipleMode() {
		return $this->multipleMode;
	}

	public function setMultipleMode($mode) {
		$this->multipleMode = $mode;
	}

	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	public function getNamespace() {
		return $this->namespace;
	}

	public function getUnusedElement() {
		$containerSection = clone $this;
		$namespacedName = $this->namespace;

		$key = count($this->getElements());

		$itemSection = $containerSection->createElement($namespacedName . "." . $key, $this->type.'Item');

		$class = $this->annotations->getClass();
		$object = new $class();

		if(!is_object($this->modelFormFactory->form))
			$this->modelFormFactory->form = $this->getRootForm();
        $elements = $this->modelFormFactory->generateElements($object, $itemSection, $namespacedName . "." . $key);

        return $itemSection;
	}

	public function getTemplate() {
		$containerSection = clone $this;
		$namespacedName = "_template.".$this->namespace;

		$itemSection = $containerSection->createElement($namespacedName . ".000", $this->type.'Item');

		$class = $this->annotations->getClass();
		$object = new $class();

		if(!is_object($this->modelFormFactory->form))
			$this->modelFormFactory->form = $this->getRootForm();
        $elements = $this->modelFormFactory->generateElements($object, $itemSection, $namespacedName . ".000");

        return $itemSection;
	}

	public function getNextKey() {
		return count($this->getElements()) + 1;
	}
}
?>