<?php
namespace TYPO3\Admin\Finishers;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              *
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
    * TODO: Document this Method! ( executeInternal )
    */
    public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$nextRequest = clone $formRuntime->getRequest()->getParentRequest();

			// TODO: make configurable
		$nextRequest->setArgument('@action', 'update');

		$objectArgument = $formRuntime->getFormState()->getFormValue('object');
		if (isset($this->options['objectIdentifier'])) {
			$objectArgument['__identity'] = $this->options['objectIdentifier'];
		}
		$nextRequest->setArgument('object', $objectArgument);

		$forwardException = new \TYPO3\FLOW3\Mvc\Exception\ForwardException();
		$nextRequest->setDispatched(FALSE);
		$forwardException->setNextRequest($nextRequest);
		throw $forwardException;
    }

}

?>