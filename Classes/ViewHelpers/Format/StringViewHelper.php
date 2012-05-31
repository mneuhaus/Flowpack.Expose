<?php

namespace Foo\ContentManagement\ViewHelpers\Format;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
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
 * TODO: (SK) implement opposite direction in property mapper and then get rid of this view helper
 * 		 (MN) See comments in the Propertymapper
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class StringViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \Foo\ContentManagement\Core\PropertyMapper
	 * @api
	 * @FLOW3\Inject
	 */
	protected $propertyMapper;

	/**
	 *
	 * @param mixed $value
	 * @return string Rendered string
		 * @api
	 */
	public function render($value) {
		$options = array(
		 //    array(
			// 'TYPO3\FLOW3\Property\TypeConverter\DateTimeConverter',
			// \TYPO3\FLOW3\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
			// $this->parentProperty->representation->datetimeFormat
		 //    )
		);
		$string = $this->propertyMapper->convert($value, "string", \Foo\ContentManagement\Core\PropertyMappingConfiguration::getConfiguration('\Foo\ContentManagement\Core\PropertyMappingConfiguration', $options));
		return $string;
	}
}

?>