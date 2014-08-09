<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * You can use this viewHelper to retrieve a property from ab object based on the name of the property stored in a variabl
 *
 * Example
 * =======
 *
 * .. code-block:: html
 *
 *   <f:for each="{properties}" as="property">
 *     <e:property object="{object}" name="{property}" />
 *   </f:for>
 *
 */
class PropertyViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 *
	 * @param object $object Object to get the property or propertyPath from
	 * @param string $name Name of the property or propertyPath
	 * @return string
	 */
	public function render($object, $name) {
		if (method_exists($object, $name)) {
			return $object->$name();
		}
		return ObjectAccess::getPropertyPath($object, $name);
	}
}