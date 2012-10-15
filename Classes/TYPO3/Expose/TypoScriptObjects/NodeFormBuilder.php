<?php
namespace TYPO3\Expose\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Render a Node-based form
 */
class NodeFormBuilder extends ObjectFormBuilder {

	/**
	 * @param \TYPO3\Form\Core\Model\FormDefinition $formDefinition
	 * @param array $objectNamespaces
	 */
	protected function addValidatorsToForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, array $objectNamespaces) {
	}

	/**
	 * @param string $sectionName
	 * @param \TYPO3\Form\FormElements\Section $section
	 * @param string $namespace
	 * @param object $object
	 * @return void
	 */
	public function createElementsForSection($sectionName, \TYPO3\Form\FormElements\Section $section, $namespace, $object, $propertyNames = NULL) {
		/* @var $object \TYPO3\TYPO3CR\Domain\Model\NodeInterface */
		$contentType = $object->getContentType();

		$sectionDescription = $section->createElement('sectionDescription' . $namespace, 'TYPO3.Form:StaticText');
		$sectionDescription->setProperty('text', $object->getContextPath());

		$this->tsRuntime->pushContext('parentFormElement', $section);
		foreach ($contentType->getProperties() as $propertyName => $propertySchema) {
				// as we are using nodes here, we cannot rely on $propertySchema being of any particular structure.

			$this->tsRuntime->pushContext('propertyName', $propertyName);
			$this->tsRuntime->pushContext('formElementIdentifier', $namespace . '.' . $propertyName);
			$this->tsRuntime->pushContext('propertySchema', $propertySchema);
			$this->tsRuntime->pushContext('propertyType', (isset($propertySchema['type']) ? $propertySchema['type'] : 'string'));
			$this->tsRuntime->pushContext('propertyElementType', isset($propertySchema['elementType']) ? $propertySchema['elementType'] : NULL);
			$this->tsRuntime->pushContext('formBuilder', $this);
			$this->tsRuntime->pushContext('propertyValue', $this->getPropertyValue($object, $propertyName));

			$this->tsRuntime->render($this->path . '/elementBuilder');

			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
		}
		$this->tsRuntime->popContext();
	}

	/**
	 * @param object $object
	 * @return array
	 */
	protected function getObjectIdentifierArrayForObject($object) {
		return array('__contextNodePath' => $object->getContextPath());
	}

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $object
	 * @return string
	 */
	protected function getLabelForObject($object) {
		return $object->getLabel();
	}

	/**
	 * @param \TYPO3\Form\Core\Model\FormDefinition $formDefinition
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $object
	 * @param string $namespace
	 * @return void
	 */
	protected function loadDefaultValuesIntoForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $object, $namespace) {
		foreach ($object->getProperties() as $propertyName => $propertyValue) {
			$formElement = $formDefinition->getElementByIdentifier($namespace . '.' . $propertyName);
			if ($formElement !== NULL) {
				$formElement->setDefaultValue($propertyValue);
			}
		}
		$additionalProperties = array('hidden', 'hiddenBeforeDateTime', 'hiddenAfterDateTime');

		foreach ($additionalProperties as $internalPropertyName) {
			$formElement = $formDefinition->getElementByIdentifier($namespace . '._' . $internalPropertyName);
			if ($formElement !== NULL) {
				$formElement->setDefaultValue(\TYPO3\Flow\Reflection\ObjectAccess::getProperty($object, $internalPropertyName));
			}
		}
	}

	public function getPropertyValue($object, $propertyName) {
		if ($object->hasProperty($propertyName)) {
			return $object->getProperty($propertyName);
		}

		if (\TYPO3\Flow\Reflection\ObjectAccess::isPropertyGettable($object, $propertyName)) {
			return \TYPO3\Flow\Reflection\ObjectAccess::getProperty($object, $propertyName);
		}

		return NULL;
	}
}
?>