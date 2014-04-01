<?php
namespace TYPO3\Expose\Form\Elements;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A password with confirmation form element
 */
class PasswordWithHashing extends \TYPO3\Form\FormElements\PasswordWithConfirmation {

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * @var string
	 */
	protected $defaultValue;

	/**
	 * @param \TYPO3\Form\Core\Runtime\FormRuntime $formRuntime
	 * @param mixed $elementValue
	 * @return void
	 */
	public function onSubmit(\TYPO3\Form\Core\Runtime\FormRuntime $formRuntime, &$elementValue) {
		parent::onSubmit($formRuntime, $elementValue);
		if (strlen($elementValue) > 0) {
			$elementValue = $this->hashService->hashPassword($elementValue, 'default');
		} else {
			$elementValue = $this->defaultValue;
		}
	}

	public function getConfirmationUniqueIdentifier() {
		return $this->getUniqueIdentifier() . '-confirmation';
	}

	/**
	 * Set the default value of the element
	 *
	 * @param mixed $defaultValue
	 * @return void
	 */
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
		$formDefinition = $this->getRootForm();
		$formDefinition->addElementDefaultValue($this->identifier, $defaultValue);
	}
}

?>