<?php
namespace TYPO3\Expose\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
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
final class Label {

    /**
     * @var string
     **/
    protected $label = NULL;

    /**
     * @param string $value
     */
    public function __construct(array $values = array()) {
        $this->label = isset($values['value']) ? $values['value'] : $this->label;
        $this->label = isset($values['label']) ? $values['label'] : $this->label;
    }

    /**
    * Return the current label
    *
    * @return string
    */
    public function getLabel() {
        return $this->label;
    }

}

?>