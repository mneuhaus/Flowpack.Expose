<?php
namespace TYPO3\Expose\ViewHelpers\Render;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @api
 */
class ListViewHelper extends AbstractRenderViewHelper {

	/**
	 * Renders the content.
	 *
	 * @param array $objects
	 * @param string $variant
	 * @param array $arguments
	 * @return string
	 * @api
	 */
	public function render(array $objects = array(), $variant = 'Table', array $arguments = array()) {
		return $this->view->renderContent('List', array_merge($arguments, array('objects' => $objects)), $variant);
	}
}

?>