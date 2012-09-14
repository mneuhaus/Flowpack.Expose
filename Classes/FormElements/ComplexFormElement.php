<?php
namespace TYPO3\Expose\FormElements;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
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

    /**
    * TODO: Document this Method! ( setAnnotations )
    */
    public function setAnnotations($annotations) {
        $this->properties['annotations'] = $annotations;
    }

}

?>