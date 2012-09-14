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
final class Cache implements SingleAnnotationInterface {

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param string $value
     */
    public function __construct(array $values) {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->name;
    }

    /**
    * TODO: Document this Method! ( getName )
    */
    public function getName() {
        return $this->name;
    }

}

?>