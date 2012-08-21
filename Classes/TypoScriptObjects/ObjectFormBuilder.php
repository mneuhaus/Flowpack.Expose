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
	 * if set, the objects being used
	 *
	 * @var object
	 */
	protected $objects = NULL;

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

	public function setObjects($objects) {
		$this->objects = $objects;
	}

	public function setFormIdentifier($formIdentifier) {
		$this->formIdentifier = $formIdentifier;
	}

	public function setFormPresetName($formPresetName) {
		$this->formPresetName = $formPresetName;
	}

    /**
     * Evaluate the collection nodes
     *
     * @return string
     */
    public function evaluate() {
		$formDefinition = $this->baseFormFactory->build(array('identifier' => $this->tsValue('formIdentifier')), $this->tsValue('formPresetName'));
		$page = $formDefinition->createPage('page1');

		$forwardFinisher = new \TYPO3\Admin\Finishers\ControllerCallbackFinisher();
		$formDefinition->addFinisher($forwardFinisher);

		$objectNamespaces = array();
		if ($this->objects !== NULL && count($this->objects) > 0) {
			$i = 0;
			$objectIdentifiers = array();
			foreach ($this->tsValue('objects') as $object) {
				$this->createFormForSingleObject($page, $object, 'objects.' . $i);
				$objectNamespaces[] = 'objects.' . $i;
				$this->loadDefaultValuesIntoForm($formDefinition, $object, 'objects.' . $i);
				$objectIdentifiers[] = $this->getObjectIdentifierArrayForObject($object);
				$i++;
			}

			$forwardFinisher->setOption('objectIdentifiers', $objectIdentifiers);
		} else {
			$this->createFormForSingleObject($page, NULL, 'objects.0.');
			$objectNamespaces[] = 'objects.0';
		}

		$this->addValidatorsToForm($formDefinition, $objectNamespaces);
		return $formDefinition;
	}
	protected function getObjectIdentifierArrayForObject($object) {
		return array('__identity' => $this->persistenceManager->getIdentifierByObject($object));
	}

	protected function createFormForSingleObject(\TYPO3\Form\Core\Model\AbstractSection $parentFormElement, $object, $namespace = '') {
		$sectionNames = $this->findSections();
		foreach ($sectionNames as $sectionName) {
			// TODO: Handle recursive sections
			// TODO: clean up code
			$this->tsRuntime->pushContext('parentFormElement', $parentFormElement);
			$this->tsRuntime->pushContext('identifier', $sectionName . '.' . $namespace);
			$section = $this->tsRuntime->render($this->path . '/sectionBuilder');
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();

			$section->setLabel($this->getLabelForObject($object));
			$this->createElementsForSection($sectionName, $section, $namespace, $object);
		}
    }

	protected function getLabelForObject($object) {

	}

	protected function addValidatorsToForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $objectNamespaces) {
		$className = $this->tsValue('className');
		$baseValidator = $this->validatorResolver->getBaseValidatorConjunction($className, array('Default', 'Form'));
		/* @var $baseValidator \TYPO3\FLOW3\Validation\Validator\ConjunctionValidator */
		foreach ($baseValidator->getValidators() as $validator) {
			if ($validator instanceof \TYPO3\FLOW3\Validation\Validator\GenericObjectValidator) {
				/* @var $validator \TYPO3\FLOW3\Validation\Validator\GenericObjectValidator */
				foreach ($validator->getPropertyValidators() as $propertyName => $propertyValidatorList) {
					foreach ($objectNamespaces as $objectNamespace) {
						$formElement = $formDefinition->getElementByIdentifier($objectNamespace . '.' . $propertyName);
						if ($formElement !== NULL) {
							foreach ($propertyValidatorList as $propertyValidator) {
								$formElement->addValidator($propertyValidator);
							}
						}
					}
				}
			} else {
				// TODO: implement ELSE-case for other validators
			}
		}
	}

	protected function loadDefaultValuesIntoForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $object, $namespace) {
		$properties = \TYPO3\FLOW3\Reflection\ObjectAccess::getGettableProperties($object);
		foreach ($properties as $propertyName => $propertyValue) {
			$formElement = $formDefinition->getElementByIdentifier($namespace . '.' . $propertyName);
			if ($formElement !== NULL) {
				$formElement->setDefaultValue($propertyValue);
			}
		}
	}

	protected function findSections() {
		// TODO implement
		return array('Default');
	}

	protected function createElementsForSection($sectionName, \TYPO3\Form\FormElements\Section $section, $namespace, $object) {
		// TODO evaluate $sectionName
		$className = $this->tsValue('className');
		$propertyNames = $this->reflectionService->getClassPropertyNames($className);
		$classSchema = $this->reflectionService->getClassSchema($className);

		$this->tsRuntime->pushContext('parentFormElement', $section);
		foreach ($propertyNames as $propertyName) {
			$propertySchema = $classSchema->getProperty($propertyName);

			$this->tsRuntime->pushContext('className', $className);
			$this->tsRuntime->pushContext('propertyName', $propertyName);
			$this->tsRuntime->pushContext('formElementIdentifier', $namespace . '.' . $propertyName);
			$this->tsRuntime->pushContext('propertyAnnotations', $this->reflectionService->getPropertyAnnotations($className, $propertyName));
			$this->tsRuntime->pushContext('propertyType', $propertySchema['type']);
			$this->tsRuntime->pushContext('propertyElementType', $propertySchema['elementType']);

			$section = $this->tsRuntime->render($this->path . '/elementBuilder');

			$this->tsRuntime->popContext();
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