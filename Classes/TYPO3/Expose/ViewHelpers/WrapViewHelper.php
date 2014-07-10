<?php
namespace TYPO3\Expose\ViewHelpers;

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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class WrapViewHelper extends AbstractViewHelper {
	/**
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return string Rendered string
	 * @api
	 */
	public function render($name, $arguments) {
		$content = $this->renderChildren();
		if ($this->viewHelperVariableContainer->exists('TYPO3\Expose\ViewHelpers\WrapViewHelper', $name)) {
			$wraps = $this->viewHelperVariableContainer->get('TYPO3\Expose\ViewHelpers\WrapViewHelper', $name);
			foreach ($wraps as $wrap) {
				$content = $wrap->wrap($content, $arguments);
			}
		}
		return $content;
	}
}

?>