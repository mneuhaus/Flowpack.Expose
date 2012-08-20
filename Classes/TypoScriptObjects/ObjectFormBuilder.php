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
 * Render a Form using the Form framework
 *
 * // REVIEWED for release
 */
class ObjectFormBuilder extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Admin\TypoScriptObjects\Helpers\BaseFormFactory
	 */
	protected $baseFormFactory;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Validation\ValidatorResolver
	 */
	protected $validatorResolver;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * if set, the object being used
	 *
	 * @var object
	 */
	protected $object = NULL;

	/**
	 * The identifier for the current object being edited
	 *
	 * @var string
	 */
	protected $currentObjectIdentifier = NULL;

	/**
	 * @var string
	 */
	protected $formIdentifier;

	/**
	 * @var string
	 */
	protected $formPresetName;

	public function setClassName($className) {
		$this->className = $className;
	}

	public function setObject($object) {
		$this->object = $object;
	}

	public function setFormIdentifier($formIdentifier) {
		$this->formIdentifier = $formIdentifier;
	}

	public function setFormPresetName($formPresetName) {
		$this->formPresetName = $formPresetName;
	}

	public function setCurrentObjectIdentifier($currentObjectIdentifier) {
		$this->currentObjectIdentifier = $currentObjectIdentifier;
	}


    /**
     * Evaluate the collection nodes
     *
     * @return string
     */
    public function evaluate() {
		$formDefinition = $this->baseFormFactory->build(array('identifier' => $this->tsValue('formIdentifier')), $this->tsValue('formPresetName'));
		$page = $formDefinition->createPage('page1');

		$sectionNames = $this->findSections();
		foreach ($sectionNames as $sectionName) {
			// TODO: Handle recursive sections
			// TODO: clean up code
			$this->tsRuntime->pushContext('parentFormElement', $page);
			$this->tsRuntime->pushContext('identifier', $sectionName);
			$section = $this->tsRuntime->render($this->path . '/sectionBuilder');
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();

			$this->createElementsForSection($sectionName, $section);
		}

		$this->addValidatorsToForm($formDefinition);

		$forwardFinisher = new \TYPO3\Admin\Finishers\ControllerCallbackFinisher();
		$formDefinition->addFinisher($forwardFinisher);

		$object = $this->tsValue('object');
		if ($object !== NULL) {
			$this->loadDefaultValuesIntoForm($formDefinition, $object);
			$objectIdentifier = $this->persistenceManager->getIdentifierByObject($object);
			$forwardFinisher->setOption('objectIdentifier', $objectIdentifier);
		}


		return $formDefinition;
    }

	protected function addValidatorsToForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition) {
		$className = $this->tsValue('className');
		$baseValidator = $this->validatorResolver->getBaseValidatorConjunction($className, array('Default', 'Form'));
		/* @var $baseValidator \TYPO3\FLOW3\Validation\Validator\ConjunctionValidator */
		foreach ($baseValidator->getValidators() as $validator) {
			if ($validator instanceof \TYPO3\FLOW3\Validation\Validator\GenericObjectValidator) {
				/* @var $validator \TYPO3\FLOW3\Validation\Validator\GenericObjectValidator */
				foreach ($validator->getPropertyValidators() as $propertyName => $propertyValidatorList) {
					$formElement = $formDefinition->getElementByIdentifier($this->tsValue('currentObjectIdentifier') . '.' . $propertyName);
					if ($formElement !== NULL) {
						foreach ($propertyValidatorList as $propertyValidator) {
							$formElement->addValidator($propertyValidator);
						}
					}
				}
			}
		}
	}

	protected function loadDefaultValuesIntoForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $object) {
		$properties = \TYPO3\FLOW3\Reflection\ObjectAccess::getGettableProperties($object);
		foreach ($properties as $propertyName => $propertyValue) {
			$formElement = $formDefinition->getElementByIdentifier($this->tsValue('currentObjectIdentifier') . '.' . $propertyName);
			if ($formElement !== NULL) {
				$formElement->setDefaultValue($propertyValue);
			}
		}

	}

	protected function findSections() {
		// TODO implement
		return array('Default');
	}

	protected function createElementsForSection($sectionName, \TYPO3\Form\FormElements\Section $section) {
		// TODO evaluate $sectionName
		$className = $this->tsValue('className');
		$propertyNames = $this->reflectionService->getClassPropertyNames($className);
		$classSchema = $this->reflectionService->getClassSchema($className);

		$this->tsRuntime->pushContext('parentFormElement', $section);
		foreach ($propertyNames as $propertyName) {
			$propertySchema = $classSchema->getProperty($propertyName);

			$this->tsRuntime->pushContext('className', $className);
			$this->tsRuntime->pushContext('propertyName', $propertyName);
			$this->tsRuntime->pushContext('propertyAnnotations', $this->reflectionService->getPropertyAnnotations($className, $propertyName));
			$this->tsRuntime->pushContext('propertyType', $propertySchema['type']);
			$this->tsRuntime->pushContext('propertyElementType', $propertySchema['elementType']);

			$section = $this->tsRuntime->render($this->path . '/elementBuilder');

			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
		}
		$this->tsRuntime->popContext();
	}
}
?>