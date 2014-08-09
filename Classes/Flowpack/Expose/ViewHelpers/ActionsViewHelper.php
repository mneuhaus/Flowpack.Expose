<?php
namespace Flowpack\Expose\ViewHelpers;

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
 * This viewhelper looks for actions annotated with the ``\Flowpack\Expose\Annotations\Action`` annotation and filter them
 * by the type of action specified (local, global, batch)
 *
 * Example
 * =======
 *
 * .. code-block:: html
 *
 *   <e:actions type="global">
 *     <f:for each="{actions}" key="action" as="actionAnnotation">
 *       <e:link.action action="{action}" class="{actionAnnotation.class}">
 *         {actionAnnotation.label}
 *       </e:link.action>
 *     </f:for>
 *   </e:actions>
 *
 */
class ActionsViewHelper extends AbstractViewHelper {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 *
	 * @param string $type Type of actions to return [local|global|batch]
	 * @param string $actions Variable to assign the actions into the view with
	 * @return string Rendered string
	 * @api
	 */
	public function render($type, $as = 'actions') {
		$controllerObjectName = $this->controllerContext->getRequest()->getControllerObjectName();

		$actions = array();
		foreach (get_class_methods($controllerObjectName) as $objectMethod) {
			if (substr($objectMethod, -6) === 'Action') {
				$annotations = $this->reflectionService->getMethodAnnotations('\\' . $controllerObjectName, $objectMethod, '\Flowpack\Expose\Annotations\Action');
				if (empty($annotations)) {
					continue;
				}
				$annotation = current($annotations);
				if ($annotation->type === $type) {
					$annotation->action = substr($objectMethod, 0, -6);
					$actions[$annotation->action] = $annotation;
				}
			}
		}

		$this->templateVariableContainer->add($as, $actions);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		return $content;
	}
}

?>