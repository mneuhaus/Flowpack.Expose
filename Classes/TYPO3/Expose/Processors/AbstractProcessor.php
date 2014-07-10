<?php
namespace TYPO3\Expose\Processors;


/*                                                                        *
 * This script belongs to the FLow framework.                            *
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
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class AbstractProcessor extends AbstractViewHelper {
	public function addToBlock($name, $content) {
		$block = array();
		if ($this->viewHelperVariableContainer->exists('TYPO3\Expose\ViewHelpers\BlockViewHelper', $name)) {
			$block = $this->viewHelperVariableContainer->get('TYPO3\Expose\ViewHelpers\BlockViewHelper', $name);
		}
		$block[] = $content;
		$this->viewHelperVariableContainer->addOrUpdate('TYPO3\Expose\ViewHelpers\BlockViewHelper', $name, $block);
	}

	public function addWrapper($name, $callback) {
		$wrap = array();
		if ($this->viewHelperVariableContainer->exists('TYPO3\Expose\ViewHelpers\WrapViewHelper', $name)) {
			$wrap = $this->viewHelperVariableContainer->get('TYPO3\Expose\ViewHelpers\WrapViewHelper', $name);
		}
		$wrap[] = $callback;
		$this->viewHelperVariableContainer->addOrUpdate('TYPO3\Expose\ViewHelpers\WrapViewHelper', $name, $wrap);
	}
}

?>