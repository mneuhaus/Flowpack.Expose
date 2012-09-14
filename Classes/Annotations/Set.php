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
final class Set {

    /**
     * @var boolean
     */
    protected $multiple = true;

    /**
     * @var string
     */
    public $properties = '';

    /**
     * @var string
     */
    public $title = '';

    /**
     * @param string $value
     */
    public function __construct(array $values) {
        if (isset($values['title'])) {
            $this->title = $values['title'];
        }
        if (isset($values['properties'])) {
            $this->properties = $values['properties'];
        }
    }

}

?>