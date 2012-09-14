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
final class Search {

    /**
     * @var string
     */
    public $name = '';

    /**
     * @param string $value
     */
    public function __construct(array $values) {
        $this->name = isset($values['value']) ? $values['value'] : $this->name;
        $this->name = isset($values['name']) ? $values['name'] : $this->name;
    }

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->name;
    }

}

?>