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
 * Render a Node-based form
 *
 * // REVIEWED for release
 */
class NodeFormBuilder extends ObjectFormBuilder {
	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContentTypeManager
	 */
	protected $contentTypeManager;

	protected function addValidatorsToForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $objectNamespaces) {
	}

	protected function createElementsForSection($sectionName, \TYPO3\Form\FormElements\Section $section, $namespace, $object) {
		// TODO evaluate $sectionName
		/* @var $object \TYPO3\TYPO3CR\Domain\Model\NodeInterface */
		$contentType = $this->contentTypeManager->getContentType($object->getContentType());

		$this->tsRuntime->pushContext('parentFormElement', $section);
		foreach ($contentType->getProperties() as $propertyName => $propertySchema) {
				// as we are using nodes here, we cannot rely on $propertySchema being of any particular structure.

			$this->tsRuntime->pushContext('propertyName', $propertyName);
			$this->tsRuntime->pushContext('formElementIdentifier', $namespace . '.' . $propertyName);
			$this->tsRuntime->pushContext('propertySchema', $propertySchema);
			$this->tsRuntime->pushContext('propertyType', (isset($propertySchema['type']) ? $propertySchema['type'] : 'string'));

			$section = $this->tsRuntime->render($this->path . '/elementBuilder');

			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
			$this->tsRuntime->popContext();
		}
		$this->tsRuntime->popContext();
	}

	protected function getObjectIdentifierArrayForObject($object) {
		/* @var $object \TYPO3\TYPO3CR\Domain\Model\NodeInterface */
		return array('__contextNodePath' => $object->getContextPath());
	}

	protected function getLabelForObject($object) {
		/* @var $object \TYPO3\TYPO3CR\Domain\Model\NodeInterface */
		return $object->getLabel();
	}

	protected function loadDefaultValuesIntoForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition, $object, $namespace) {
		/* @var $object \TYPO3\TYPO3CR\Domain\Model\NodeInterface */
		foreach ($object->getProperties() as $propertyName => $propertyValue) {
			$formElement = $formDefinition->getElementByIdentifier($namespace . '.' . $propertyName);
			if ($formElement !== NULL) {
				$formElement->setDefaultValue($propertyValue);
			}
		}

	}
}
?>