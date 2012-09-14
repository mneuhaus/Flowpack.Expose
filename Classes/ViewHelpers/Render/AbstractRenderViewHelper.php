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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @api
 */
abstract class AbstractRenderViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initialize() {
		$this->view = new \TYPO3\Expose\View\FallbackTemplateView();
		$this->view->setControllerContext($this->controllerContext);
		$this->view->setRenderingContext($this->renderingContext);
	}

	/**
	 * If $arguments['settings'] is not set, it is loaded from the TemplateVariableContainer (if it is available there).
	 *
	 * @param array $arguments
	 * @return array
	 */
	protected function loadSettingsIntoArguments(array $arguments) {
		if (!isset($arguments['settings']) && $this->templateVariableContainer->exists('settings')) {
			$arguments['settings'] = $this->templateVariableContainer->get('settings');
		}

		return $arguments;
	}

	/**
	 * Renders the content.
	 *
	 * @param array $objects
	 * @return string
	 * @api
	 */
	public function render(array $objects = array()) {
		return $this->view->renderContent('List', array('objects' => $objects));
	}
}

?>