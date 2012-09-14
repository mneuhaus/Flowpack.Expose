<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
* This script belongs to the TYPO3.Expose package.              *
*                                                                        *
* It is free software; you can redistribute it and/or modify it under    *
* the terms of the GNU Lesser General Public License as published by the *
* Free Software Foundation, either version 3 of the License, or (at your *
* option) any later version.                                             *
*                                                                        *
* This script is distributed in the hope that it will be useful, but     *
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
* TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
* General Public License for more details.                               *
*                                                                        *
* You should have received a copy of the GNU Lesser General Public       *
* License along with the script.                                         *
* If not, see http://www.gnu.org/licenses/lgpl.html                      *
*                                                                        *
* The TYPO3 project - inspiring people to share!                         *
*                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
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