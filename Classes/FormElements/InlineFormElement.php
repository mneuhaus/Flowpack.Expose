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
    * TODO: Document this Method! ( getAnnotations )
    */
    public function getAnnotations() {
        return $this->annotations;
    }

    /**
    * TODO: Document this Method! ( setAnnotations )
    */
    public function setAnnotations($annotations) {
        $this->annotations = $annotations;
    }

    /**
    * TODO: Document this Method! ( getMultipleMode )
    */
    public function getMultipleMode() {
        return $this->multipleMode;
    }

    /**
    * TODO: Document this Method! ( setMultipleMode )
    */
    public function setMultipleMode($mode) {
        $this->multipleMode = $mode;
    }

    /**
    * TODO: Document this Method! ( getNamespace )
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
    * TODO: Document this Method! ( setNamespace )
    */
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /**
    * TODO: Document this Method! ( getNextKey )
    */
    public function getNextKey() {
        return count($this->getElements()) + 1;
    }

    /**
    * TODO: Document this Method! ( getPropertyNames )
    */
    public function getPropertyNames() {
        $propertyNames = array();
        foreach ($this->getAnnotations()->getProperties() as $property) {
            $propertyNames[] = $property->getLabel();
        }
        return $propertyNames;
    }

    /**
    * TODO: Document this Method! ( getTemplate )
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
    * TODO: Document this Method! ( getUnusedElement )
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