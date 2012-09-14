<?php
namespace TYPO3\Expose\Annotations;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * @Annotation
 */
final class Inline implements SingleAnnotationInterface {

	/**
	 * @var string
	 */
	protected $variant = 'TYPO3.Expose:InlineTabular';

	/**
	 * @param array $values
	 */
	public function __construct(array $values = array()) {
		$this->variant = isset($values['value']) && $values['value'] !== TRUE ? $values['value'] : $this->variant;
		$this->variant = isset($values['variant']) ? $values['variant'] : $this->variant;
	}

	/**
	 * TODO: Document this Method! ( getAmount )
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @return string
	 */
	public function getVariant() {
		return $this->variant;
	}
}

?>