<?php
namespace TYPO3\Expose\ViewHelpers\Render;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Render a Action for an Object or class
 *
 * The factory class must implement {@link TYPO3\Form\Factory\FormFactoryInterface}.
 *
 * @api
 */
class ActionViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $defaultAction name of the entry action
	 * @param string $defaultController name of the entry controller
	 * @param array $defaultArguments for the controller
	 * @return string the rendered form
	 */
	public function render($defaultAction = 'index', $defaultController = 'TYPO3\Expose\Controller\IndexController', $defaultArguments = array()) {
		$response = new \TYPO3\Flow\Http\Response($this->controllerContext->getResponse());
		$request = $this->controllerContext->getRequest();

		$exposeRuntime = new \TYPO3\Expose\Core\ExposeRuntime($request, $response);
        $exposeRuntime->setDefaultExposeControllerClassName($defaultController);
        $exposeRuntime->setDefaultExposeControllerArguments($defaultArguments);
        return $exposeRuntime->execute();
	}
}

?>