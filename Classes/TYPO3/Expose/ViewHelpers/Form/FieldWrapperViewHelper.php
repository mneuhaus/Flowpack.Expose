<?php
namespace TYPO3\Expose\ViewHelpers\Form;

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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class FieldWrapperViewHelper extends AbstractViewHelper {
	/**
	 *
	 * @param string $id
	 * @param string $label
	 * @param string $property
	 * @return string Rendered string
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @api
	 */
	public function render($id, $label, $property) {
		$formGroupClass = 'form-group';
		$helpBlock = '';

		$request = $this->controllerContext->getRequest();
		$validationResults = $request->getInternalArgument('__submittedArgumentValidationResults');
		$formObjectName = $this->viewHelperVariableContainer->get('TYPO3\Fluid\ViewHelpers\FormViewHelper', 'formObjectName');
		if ($validationResults !== NULL && $property !== '') {
			$validationResults = $validationResults->forProperty($formObjectName . '.' . $property);
			if (count($validationResults->getErrors()) > 0) {
				$formGroupClass.= ' has-error';
				foreach ($validationResults->getErrors() as $error) {
					$helpBlock.= '<span class="help-block">' . $error->getMessage() . '</span>';
				}
			}
		}

		$content = '<div class="' . $formGroupClass . '">';
		$content.= '<label for="' . $id . '" class="col-sm-2 control-label">' . $label . '</label>';
		$content.= '<div class="col-sm-10">';
		$content.= $this->renderChildren();
		$content.= $helpBlock;
		$content.= '</div>';
		$content.= '</div>';
		return $content;
	}
}

?>