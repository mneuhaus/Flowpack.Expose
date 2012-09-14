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
class OptionsFormElement extends ComplexFormElement {

    /**
    * TODO: Document this Method! ( getAnnotations )
    */
    public function getAnnotations() {
        return $this->properties['annotations'];
    }

    /**
    * TODO: Document this Method! ( getOptionsProvider )
    */
    public function getOptionsProvider() {
        $optionsProviderClass = (string) $this->getAnnotations()->getOptionsProvider();
        $optionsProvider = new $optionsProviderClass($this->getAnnotations());
        return $optionsProvider;
    }

    /**
    * TODO: Document this Method! ( getProperties )
    */
    public function getProperties() {
        $optionsProvider = $this->getOptionsProvider();
        $this->properties['options'] = $this->getOptionsProvider()->getOptions();
        return $this->properties;
    }

}

?>