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
final class Variant implements SingleAnnotationInterface {

    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array
     */
    public $values = array();

    /**
     * @var string
     */
    public $variant = 'Default';

    /**
     * @param string $value
     */
    public function __construct(array $values = array()) {
        $this->variant = isset($values['value']) ? $values['value'] : $this->variant;
        $this->variant = isset($values['variant']) ? $values['variant'] : $this->variant;
        $this->options = isset($values['options']) ? $values['options'] : $this->options;
        if (isset($this->options) && !is_array($this->options)) {
            $this->options = explode(',', str_replace(' ', '', $this->options));
        }
        $this->values = $values;
    }

    /**
    * TODO: Document this Method! ( getDefault )
    */
    public function getDefault() {
        return $this->variant == 'Default';
    }

    /**
    * TODO: Document this Method! ( getVariant )
    */
    public function getVariant($action = null) {
        if (isset($this->values[$action])) {
            return $this->values[$action];
        }
        return $this->variant;
    }

}

?>