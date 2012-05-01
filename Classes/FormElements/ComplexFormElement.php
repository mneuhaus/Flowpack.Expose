<?php
namespace Foo\ContentManagement\FormElements;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
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
class ComplexFormElement extends \TYPO3\Form\Core\Model\AbstractFormElement {
	const PATTERN_IDENTIFIER = '/^[a-zA-Z0-9-_\.]+$/';

	/**
	 * Constructor. Needs this FormElement's identifier and the FormElement type
	 *
	 * @param string $identifier The FormElement's identifier
	 * @param string $type The Form Element Type
	 * @api
	 */
	public function __construct($identifier, $type) {
		if (!is_string($identifier) || strlen($identifier) === 0) {
			throw new \TYPO3\Form\Exception\IdentifierNotValidException('The given identifier was not a string or the string was empty.', 1325574803);
		}
		if (preg_match(\Foo\ContentManagement\FormElements\ComplexFormElement::PATTERN_IDENTIFIER, $identifier) !== 1) {
			throw new \TYPO3\Form\Exception\IdentifierNotValidException(sprintf('The given identifier "%s" is not valid. It has to be lowerCamelCased.', $identifier), 1329131480);
		}
		$this->identifier = $identifier;
		$this->type = $type;
	}

	public function getIdSafeIdentifier() {
		$regex = array(
			"/^__/" => "internal-",
			"/\./" => "-"
		);
		return preg_replace(array_keys($regex), array_values($regex), $this->identifier);
	}

	public function getUniqueIdentifier() {
		$formDefinition = $this->getRootForm();
		return sprintf('%s-%s', $formDefinition->getIdentifier(), $this->getIdSafeIdentifier());
	}
}
?>