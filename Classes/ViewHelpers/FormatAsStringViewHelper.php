<?php

namespace TYPO3\Expose\ViewHelpers;

/* *
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

/**
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * // REVIEWED for release
 */
class FormatAsStringViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @return string Rendered string
	 */
	public function render() {
		$value = $this->renderChildren();

		$fluidTemplateTsObject = $this->templateVariableContainer->get('fluidTemplateTsObject'); // TODO: should be retrieved differently
		$path = $fluidTemplateTsObject->getPath() . '/stringFormatter<TYPO3.Expose:StringFormatter>';
		$fluidTemplateTsObject->getTsRuntime()->pushContext('value', $value);
		$output = $fluidTemplateTsObject->getTsRuntime()->render($path);
		$fluidTemplateTsObject->getTsRuntime()->popContext();

		return $output;
	}
}
?>