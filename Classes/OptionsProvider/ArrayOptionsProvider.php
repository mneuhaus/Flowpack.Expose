<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * OptionsProvider for related Beings
 *
 */
class ArrayOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

    /**
     * This functions returns the Options defined by a internal property
     * or Annotations
     *
     * @return array $options
     */
    public function getOptions() {
        $class = $this->annotations->getClass();
        $options = array();
        if (isset($this->annotations->getOptionsProvider()->property)) {
            $optionsProperty = $this->annotations->getOptionsProvider()->property;
        } else {
            $optionsProperty = '_' . $this->annotations->getProperty();
        }
        if (!empty($this->annotations->getOptionsProvider()->options)) {
            $options = $this->annotations->getOptionsProvider()->options;
        } else {
            $options = $class->getValue($optionsProperty);
        }
        return $options;
    }

}

?>