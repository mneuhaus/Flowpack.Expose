<?php
namespace Flowpack\Expose\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use Flowpack\Expose\Utility\StringFormatter;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * You can use this viewhelper to check if a property has validation errors.
 *
 * Examples
 * =======
 *
 * .. code-block:: html
 *
 *   <div class="form-group {e:form.propertyHasResults(property: someProperty, then: 'has-error')}">
 *     ...
 *   </div>
 *
 * .. code-block:: html
 *
 *   <e:form.propertyHasResults property="someProperty">
 *     This property has some errors!
 *   </e:form.propertyHasResults>
 */
class PropertyHasResultsViewHelper extends AbstractConditionViewHelper {
	/**
	 *
	 * @param string $property Name of the property to check for Validation errors
	 * @return string Rendered string
	 * @api
	 */
	public function render($property) {
		$request = $this->controllerContext->getRequest();
		$validationResults = $request->getInternalArgument('__submittedArgumentValidationResults');
		$formObjectName = $this->viewHelperVariableContainer->get('TYPO3\Fluid\ViewHelpers\FormViewHelper', 'formObjectName');

		if ($validationResults === NULL || $property === '') {
			return;
		}

		$propertyPath = StringFormatter::formNameToPath($property);
		$validationResults = $validationResults->forProperty($propertyPath);

		if (!empty($validationResults->getErrors())) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}

?>