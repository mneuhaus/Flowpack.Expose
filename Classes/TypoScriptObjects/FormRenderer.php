<?php
namespace TYPO3\Expose\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Render a Form using the Form framework
 *
 * // REVIEWED for release
 */
class FormRenderer extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject {

    /**
     * Evaluate the collection nodes
     *
     * @return string
     */
    public function evaluate() {
		$formDefinition = $this->tsRuntime->evaluate($this->path . '/form');
		if (!($formDefinition instanceof \TYPO3\Form\Core\Model\FormDefinition)) {
			throw new \Exception("TODO: FormRenderer expects a form definition inside form/");
		}
		/* @var $formDefinition \TYPO3\Form\Core\Model\FormDefinition */
		$response = new \TYPO3\FLOW3\Http\Response($this->tsRuntime->getControllerContext()->getResponse());
		$form = $formDefinition->bind($this->tsRuntime->getControllerContext()->getRequest(), $response);
		return $form->render();
    }
}
?>