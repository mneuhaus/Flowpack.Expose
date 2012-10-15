<?php
namespace TYPO3\Expose\TypoScriptObjects;

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
 * Render a Form using the Form framework
 */
class FormRenderer extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$formDefinition = $this->tsRuntime->evaluate($this->path . '/form');
		if (!($formDefinition instanceof \TYPO3\Form\Core\Model\FormDefinition)) {
			throw new \InvalidArgumentException('TODO: FormRenderer expects a form definition inside form/');
		}

		$response = new \TYPO3\Flow\Http\Response($this->tsRuntime->getControllerContext()->getResponse());
		return $formDefinition->bind($this->tsRuntime->getControllerContext()->getRequest(), $response)->render();
	}
}

?>