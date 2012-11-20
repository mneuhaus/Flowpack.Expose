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
	 * @param string $action name of the entry action
	 * @param string $controller name of the entry controller
	 * @param string $package name of the entry package
	 * @param array $arguments for the controller
	 * @param string $type to use for the action
	 * @param object $object to use for the action
	 * @param string $typoScriptPrefix prototype prefix for the TypoScript rendering
	 * @return string the rendered form
	 */
	public function render($action = 'index', $controller = 'Index', $package = 'TYPO3.Expose', $arguments = array(), $type = NULL, $object = NULL, $typoScriptPrefix = NULL) {
		$response = new \TYPO3\Flow\Http\Response($this->controllerContext->getResponse());
		$request = $this->controllerContext->getRequest();

		if ($type !== NULL) {
			$arguments['type'] = $type;
		}

		if ($object !== NULL) {
			$arguments['type'] = get_class($object);
			$arguments['objects']['0'] = $object;
		}

		$exposeRuntime = new \TYPO3\Expose\Core\ExposeRuntime($request, $response);
		$exposeRuntime->setDefaultPackage($package);
		$exposeRuntime->setDefaultController($controller);
		$exposeRuntime->setDefaultAction($action);
		$exposeRuntime->setDefaultArguments($arguments);
		$exposeRuntime->setTypoScriptPrefix($typoScriptPrefix);
		return $exposeRuntime->execute();
	}
}

?>