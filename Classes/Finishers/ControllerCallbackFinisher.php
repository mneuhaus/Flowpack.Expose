<?php
namespace Foo\ContentManagement\Finishers;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * This finisher redirects to another Controller.
 */
class ControllerCallbackFinisher extends \TYPO3\Form\Core\Model\AbstractFinisher {
	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$controllerCallback = $this->parseOption('controllerCallback');

		$controllerRequest = $formRuntime->getRequest()->getParentRequest();
		
		$controllerName = $controllerRequest->getControllerObjectName();
		$controller = $this->objectManager->get($controllerName);
		$controller->initializeController($controllerRequest, $formRuntime->getResponse()->getParentResponse());
		call_user_method_array($controllerCallback, $controller, array($formRuntime));
	}
}
?>