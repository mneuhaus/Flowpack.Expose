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
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 */
	protected $reflectionService;

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

		return $formDefinition;
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