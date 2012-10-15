<?php
namespace TYPO3\Expose\FormElements;

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
 * A generic form element
 */
class ComplexFormElement extends \TYPO3\Form\Core\Model\AbstractFormElement {
	/**
	 * @param object $annotations
	 * @return void
	 */
	public function setAnnotations($annotations) {
		$this->properties['annotations'] = $annotations;
	}
}

?>