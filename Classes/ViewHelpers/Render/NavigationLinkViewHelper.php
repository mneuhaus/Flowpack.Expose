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
class NavigationLinkViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders the content.
	 *
	 * @param object $object
	 * @return string
	 * @api
	 */
	public function render($object) {
		$linkRenderer = $this->templateVariableContainer->get('linkRenderer');

		return $linkRenderer->renderLink(array(
			'class' => get_class($object),
			'object' => $object
		));
	}
}

?>