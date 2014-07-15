<?php
namespace Flowpack\Expose\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".          *
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
final class Inline {

	/**
	 * @var string
	 */
	protected $element = 'Flowpack.Expose:InlineTabular';

	/**
	 * @param string $value
	 */
	public function __construct(array $values = array()) {
		$this->element = isset($values['value']) ? $values['value'] : $this->element;
		$this->element = isset($values['element']) ? $values['element'] : $this->element;
	}

	/**
	 * Return the current Element
	 *
	 * @return string
	 */
	public function getElement() {
		return $this->element;
	}

}

?>