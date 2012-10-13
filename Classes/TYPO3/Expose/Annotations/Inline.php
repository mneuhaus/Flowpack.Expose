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
final class Inline {

    /**
     * @var string
     **/
    protected $element = 'TYPO3.Expose:InlineTabular';

    /**
     * @param string $value
     */
    public function __construct(array $values = array()) {
        $this->element = isset($values['value']) && $values['value'] !== true ? $values['value'] : $this->element;
        $this->element = isset($values['element']) ? $values['element'] : $this->element;
    }

    /**
    * TODO: Document this Method! ( getAmount )
    */
    public function getAmount() {
        return $this->amount;
    }

    /**
    * TODO: Document this Method! ( getVariant )
    */
    public function getElement() {
        return $this->element;
    }

}

?>