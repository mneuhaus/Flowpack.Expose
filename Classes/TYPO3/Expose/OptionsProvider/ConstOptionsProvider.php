<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 *
 * This OptionsProvider is used to load options from an Entities class
 * by using a regular expression to match existing constants
 *
 * Example:
 *      TYPO3\Party\Domain\Model\ElectronicAddress:
 *          Properties:
 *              type:
 *                  Element: TYPO3.Form:SingleSelectDropdown
 *                  OptionsProvider:
 *                      Name: ConstOptionsProvider
 *                      RegEx: TYPE_.+
 *
 */
class ConstOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

    /**
     * Load the Options by searching the Entities constants based on the specified regular
     * expression
     *
     * @return array $options
     */
    public function getOptions() {
        $reflection = new \ReflectionClass($this->annotations->getClass());
        $regex = $this->annotations->getOptionsProvider()->regex;
        $constants = array();
        foreach ($reflection->getConstants() as $key => $value) {
            if (preg_match(('/' . $regex) . '/', $key)) {
                $constants[constant(($this->annotations->getClass() . '::') . $key)] = $value;
            }
        }
        return $constants;
    }

}

?>