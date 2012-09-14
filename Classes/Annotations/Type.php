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
final class Type extends ContextAwareAnnotation implements SingleAnnotationInterface {

    /**
     * @var string
     */
    public $name = 'string';

    /**
     * @var array
     */
    public $subtype = null;

    /**
     * @param string $value
     */
    public function __construct(array $values = array()) {
        $this->name = isset($values['value']) ? $values['value'] : $this->name;
        $this->name = isset($values['name']) ? $values['name'] : $this->name;
        $this->subtype = isset($values['subtype']) ? $values['subtype'] : $this->subtype;
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->name;
    }

    /**
    * TODO: Document this Method! ( getInstance )
    */
    public function getInstance() {
        return new $this->name();
    }

}

?>