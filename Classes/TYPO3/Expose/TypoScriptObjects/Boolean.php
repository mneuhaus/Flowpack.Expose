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
 * Render a Form section using the Form framework
 */
class Boolean extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 * the class name to build the form for
	 *
	 * @var boolean
	 */
	protected $value;

	/**
	 * @param boolean $value
	 * @return void
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->tsValue('value');
	}

	/**
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		return $this->getValue();
	}
}

?>