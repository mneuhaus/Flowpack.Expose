<?php
namespace Foo\ContentManagement\Finishers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
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
class ActionFinisher extends \TYPO3\Form\Core\Model\AbstractFinisher {
	/**
	 * @var \Foo\ContentManagement\Actions\ActionManager
	 * @FLOW3\Inject
	 */
	protected $actionManager;

	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$request = $formRuntime->getRequest();//->getMainRequest();
		$actionName = $request->getParentRequest()->getControllerActionName();
		$action = $this->actionManager->getActionByShortName($actionName);

		$class = $this->parseOption('class');

		$action->formFinischer($formRuntime);
	}
}
?>