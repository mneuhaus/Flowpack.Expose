<?php
namespace TYPO3\Expose\Form\Finishers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * This finisher redirects to another Controller.
 */
class ControllerCallbackFinisher extends \TYPO3\Form\Core\Model\AbstractFinisher {

	/**
	 * @return void
	 * @throws \TYPO3\Flow\Mvc\Exception\ForwardException
	 */
	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$nextRequest = clone $formRuntime->getRequest()->getParentRequest();

		$nextRequest->setArgument('@action', $this->parseOption('callbackAction'));

		$objectArguments = $formRuntime->getFormState()->getFormValue('objects');

		foreach ($objectArguments as $key => $object) {
			if (method_exists($object, '__prePersist')) {
				$object->__prePersist();
			}
		}

		$nextRequest->setArgument('__objects', $objectArguments);

		$forwardException = new \TYPO3\Flow\Mvc\Exception\ForwardException();
		$nextRequest->setDispatched(FALSE);
		$forwardException->setNextRequest($nextRequest);
		throw $forwardException;
	}

}

?>