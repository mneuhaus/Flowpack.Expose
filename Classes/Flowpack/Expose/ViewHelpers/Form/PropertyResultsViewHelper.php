<?php
namespace Flowpack\Expose\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use Flowpack\Expose\Utility\StringFormatter;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Checks if the specified property has errors and adds them as a variable to the view.
 *
 * Example
 * =======
 *
 * .. code-block:: html
 *
 *   <e:form.propertyResults property="someProperty">
 *     <f:for each="{errors}" as="error">
 *       <p class="help-block">{error.message}</p>
 *     </f:for>
 *   </e:form.propertyResults>
 *
 */
class PropertyResultsViewHelper extends AbstractViewHelper {
	/**
	 *
	 * @param string $property Name of the propert to check for Validation errors
	 * @param string $as Name of the variable the errors will be assigned into
	 * @return string Rendered string
	 * @api
	 */
	public function render($property, $as = 'errors') {
		$request = $this->controllerContext->getRequest();
		$validationResults = $request->getInternalArgument('__submittedArgumentValidationResults');
		$formObjectName = $this->viewHelperVariableContainer->get('TYPO3\Fluid\ViewHelpers\FormViewHelper', 'formObjectName');

		if ($validationResults === NULL || $property === '') {
			return;
		}

		$propertyPath = StringFormatter::formNameToPath($property);
		$validationResults = $validationResults->forProperty($propertyPath);

		if (empty($validationResults->getErrors())) {
			return;
		}

		$templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
		$templateVariableContainer->add($as, $validationResults->getErrors());
		$output = $this->renderChildren();
		$templateVariableContainer->remove($as);

		return $output;
	}
}

?>