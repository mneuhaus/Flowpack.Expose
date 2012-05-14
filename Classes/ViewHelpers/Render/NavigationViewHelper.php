<?php
 
namespace Foo\ContentManagement\ViewHelpers\Render;

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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @version $Id: ForViewHelper.php 3346 2009-10-22 17:26:10Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 * @FLOW3\Scope("prototype")
 */
class NavigationViewHelper extends AbstractRenderViewHelper {
	/**
	 * Renders the content.
	 *
	 * @param mixed $objects
	 * @param string $variant
	 * @return string
	 * @api
	 */
	public function render($objects = array(), $variant = "List") {
		$navigationProvider = new \Foo\ContentManagement\NavigationProvider\EntityNavigationProvider($objects);
		return $this->view->renderContent("Navigation", array(
			"objects" => $objects,
			"navigationProvider" => $navigationProvider,
			"linkRenderer" => $this
		), $variant);
	}

	public function renderLink($variables) {
		foreach($variables as $key => $value)
			$this->templateVariableContainer->add($key, $value);
		
		$output = $this->renderChildren();
		
		foreach($variables as $key => $value)
			$this->templateVariableContainer->remove($key);

		return $output;
	}
}

?>
