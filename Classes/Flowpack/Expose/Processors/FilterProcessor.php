<?php
namespace Flowpack\Expose\Processors;


/*                                                                        *
 * This script belongs to the FLow framework.                            *
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
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class FilterProcessor extends AbstractProcessor {

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 *
	 * @param object $query
	 * @return string Rendered string
	 * @api
	 */
	public function process($query) {
		$this->request = $this->controllerContext->getRequest();

		$schema = $this->templateVariableContainer->get('schema');
		$fieldNames = $schema->getFilterFields();
		$className = $this->templateVariableContainer->get('className');
		$classSchema = $this->reflectionService->getClassSchema($className);

		if (empty($fieldNames)) {
			return;
		}

		$fields = array();
		foreach ($fieldNames as $fieldName) {
			$fields[$fieldName] = $classSchema->getProperty($fieldName);
		}

		$filter = array();
		if( $this->request->hasArgument("filter") ){
			$filter = $this->request->getArgument("filter");

			$constraints = array();
			foreach ($filter as $property => $value) {
				$constraints[] = $query->equals($property, $value);
			}
			$query->matching($query->logicalAnd(
				$query->getConstraint(),
				$query->logicalAnd($constraints)
			));
		}



		$this->viewHelperVariableContainer->add('Flowpack\Expose\Processor\FilterProcessor', 'filter', $filter);
		$content = $this->viewHelperVariableContainer->getView()->renderPartial('Filter', NULL, array(
			'filter' => $filter,
			'fields' => $fields
		));
		$this->viewHelperVariableContainer->remove('Flowpack\Expose\Processor\FilterProcessor', 'filter');
		$this->addToBlock('sidebar', $content);
	}
}

?>