<?php
namespace TYPO3\Admin\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Render a Form section using the Form framework
 *
 * // REVIEWED for release
 */
class DefaultFormElementBuilder extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject {
    /**
     * @var \TYPO3\Admin\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var \TYPO3\Form\Core\Model\AbstractSection
	 */
	protected $parentFormElement;

	/**
	 * @var string
	 */
	protected $formFieldType;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var array
	 */
	protected $propertyAnnotations;

	/**
	 * @var string
	 */
	protected $propertyName;

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @var string
	 */
	protected $propertyType;

	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	public function setParentFormElement($parentFormElement) {
		$this->parentFormElement = $parentFormElement;
	}

	public function setFormFieldType($formFieldType) {
		$this->formFieldType = $formFieldType;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function setPropertyAnnotations($propertyAnnotations) {
		$this->propertyAnnotations = $propertyAnnotations;
	}

	public function setPropertyName($propertyName) {
		$this->propertyName = $propertyName;
	}

	public function setClassName($className) {
		$this->className = $className;
	}

	public function setPropertyType($propertyType) {
		$this->propertyType = $propertyType;
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

		$annotations = $this->tsValue("propertyAnnotations");
		if (isset($annotations['TYPO3\Admin\Annotations\Ignore'])){
			return NULL;
		}

		/* @var $parentFormElement \TYPO3\Form\Core\Model\AbstractSection */
		$element = $parentFormElement->createElement($this->tsValue('identifier'), $this->tsValue('formFieldType'));

		if (method_exists($element, 'setAnnotations')){
			$classAnnotations = $this->annotationService->getClassAnnotations($this->tsValue("className"));
			$element->setAnnotations($classAnnotations->getPropertyAnnotations($this->tsValue("propertyName")));
		}

		$element->setDataType($this->tsValue("propertyType"));

		/* @var $element \TYPO3\Form\Core\Model\AbstractFormElement */
		$element->setLabel($this->tsValue('label'));

		return $element;
    }

}
?>