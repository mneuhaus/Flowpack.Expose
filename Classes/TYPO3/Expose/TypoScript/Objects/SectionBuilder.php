<?php
namespace TYPO3\Expose\TypoScript\Objects;

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
 * Render a Form section using the Form framework
 */
class SectionBuilder extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var \TYPO3\Form\Core\Model\AbstractSection
	 */
	protected $parentFormElement;

	/**
	 * @param string $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * @param mixed $parentFormElement
	 * @return void
	 */
	public function setParentFormElement($parentFormElement) {
		$this->parentFormElement = $parentFormElement;
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$parentFormElement = $this->tsValue('parentFormElement');
		if (!($parentFormElement instanceof \TYPO3\Form\Core\Model\AbstractSection)) {
			throw new \InvalidArgumentException('TODO: parent form element must be a section-like element');
		}

		$formFieldType = $this->tsValue('formFieldType');

		return $parentFormElement->createElement($this->tsValue('identifier'), $formFieldType);
	}
}

?>