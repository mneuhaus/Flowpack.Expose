<?php

namespace Foo\ContentManagement\NavigationProvider;

/*                                                                       *
* This script belongs to the Foo.ContentManagement package.              *
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
 * TODO: (SK) is this class realy needed? seems only like a wrapper of an $items array...
 *
 * OptionsProvider for related Beings
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Marc Neuhaus <marc@mneuhaus.com>
 */
abstract class AbstractNavigationProvider implements NavigationProviderInterface, \RecursiveIterator {
    private $items;
    private $position = 0;

    public function __construct($items) {
    	if(is_object($items)){
    		$items = iterator_to_array($items);
    	}
        $this->items = $items;
    }

    public function valid() {
        return isset($this->items[$this->position]);
    }

    public function hasChildren() {
        return is_array($this->items[$this->position]);
    }

    public function next() {
        $this->position++;
    }

    public function current() {
        return $this->items[$this->position];
    }

    public function getChildren() {
    	$class = get_class($this);
        return new $class($this->items[$this->position]);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function key() {
        return $this->position;
    }

}

?>