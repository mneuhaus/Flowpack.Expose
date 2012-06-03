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
	 * @var \Foo\ContentManagement\Core\ActionManager
	 * @FLOW3\Inject
	 */
	protected $actionManager;

	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$requestHandler = new \Foo\ContentManagement\Core\RequestHandler($formRuntime->getRequest()->getParentRequest());
		$arguments = array_merge(
			$formRuntime->getRequest()->getParentRequest()->getArguments(),
			array("formValues" => serialize($formRuntime->getFormState()->getFormValues()))
		);
		$this->actionManager->setFormRuntime($formRuntime);
		$requestHandler->forward($this->parseOption('targetAction'), NULL, NULL, $arguments);
	}
}
?>