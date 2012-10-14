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
     * @var object
     **/
    protected $annotations;

    /**
     *
     * @var string
     **/
    protected $counter = 0;

    /**
     *
     * @var string
     **/
    protected $var;

    /**
     *
     * @var object
     **/
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
    public function isMultipleMode() {
        if (isset($this->annotations["Doctrine\ORM\Mapping\ManyToMany"]) || isset($this->annotations["Doctrine\ORM\Mapping\OneToMany"])){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function setFormBuilder($formBuilder) {
        $this->formBuilder = $formBuilder;
    }

    /**
    * TODO: Document this Method! ( getNextKey )
    */
    public function getNextKey() {
        return count($this->getElements()) + 1;
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

    public function setClass($class) {
        $this->class = $class;
    }

    public function getClass() {
        return $this->class;
    }

    public function setCounter($counter) {
        $this->counter = $counter;
    }

    /**
    * TODO: Document this Method! ( getPropertyNames )
    */
    public function getPropertyNames() {
        $class = $this->getClass();
        $propertyNames = $this->reflectionService->getClassPropertyNames($class);
        return $propertyNames;
    }

    /**
    * TODO: Document this Method! ( getTemplate )
    */
    public function getTemplate() {
        $class = $this->getClass();
        $object = new $class();
        $namespace = '_template.' . $this->getIdentifier() . '.000';
        $parentSection = clone $this;
        $inlineElement = $this->annotations["TYPO3\Expose\Annotations\Inline"][0]->getElement();
        $containerSection = $parentSection->createElement($namespace, $inlineElement . 'Item');
        $section = $this->formBuilder->createFormForSingleObject($containerSection, $object, $namespace);
        return $containerSection;
    }

    /**
    * TODO: Document this Method! ( getUnusedElement )
    */
    public function getUnusedElement() {
        $class = $this->getClass();
        $object = new $class();
        $namespace = $this->getIdentifier();
        if($this->isMultipleMode()){
            $namespace.= "." . $this->counter;
        }
        $inlineElement = $this->annotations["TYPO3\Expose\Annotations\Inline"][0]->getElement();
        $containerSection = $this->createElement($namespace, $inlineElement . 'Item');
        $containerSection->setAnnotations($this->annotations);
        $section = $this->formBuilder->createElementsForSection(count($this->renderables), $containerSection, $namespace, $object);
        return $containerSection;
    }
}

?>