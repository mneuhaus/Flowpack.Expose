<?php
namespace TYPO3\Admin\NavigationProvider;

/*                                                                       *
* This script belongs to the TYPO3.Admin package.              *
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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * OptionsProvider
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractNavigationProvider implements NavigationProviderInterface, \RecursiveIterator {

    /**
    * TODO: Document this Property!
    */
    protected $items = array();

    /**
    * TODO: Document this Property!
    */
    protected $position = 0;

    /**
    * TODO: Document this Method! ( __construct )
    */
    public function __construct($options) {
        $this->options = $options;
    }

    /**
    * TODO: Document this Method! ( getChildren )
    */
    public function getChildren() {
        $class = get_class($this);
        return new $class($this->items[$this->position]);
    }

    /**
    * TODO: Document this Method! ( hasChildren )
    */
    public function hasChildren() {
        return is_array($this->items[$this->position]);
    }

    /**
    * TODO: Document this Method! ( current )
    */
    public function current() {
        return $this->items[$this->position];
    }

    /**
    * TODO: Document this Method! ( key )
    */
    public function key() {
        return $this->position;
    }

    /**
    * TODO: Document this Method! ( next )
    */
    public function next() {
        $this->position++;
    }

    /**
    * TODO: Document this Method! ( rewind )
    */
    public function rewind() {
        $this->position = 0;
    }

    /**
    * TODO: Document this Method! ( valid )
    */
    public function valid() {
        return isset($this->items[$this->position]);
    }

}

?>