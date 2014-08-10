<?php
namespace Flowpack\Expose\QueryBehaviors;

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
use Flowpack\Expose\Core\QueryBehaviors\AbstractQueryBehavior;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 */
class FilterBehavior extends AbstractQueryBehavior {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 *
	 * @param object $query
	 * @return string Rendered string
	 * @api
	 */
	public function run($query) {
		$schema = $this->templateVariableContainer->get('schema');
		$properties = $schema->getFilterProperties();
		$className = $this->templateVariableContainer->get('className');
		$classSchema = $this->reflectionService->getClassSchema($className);

		if (empty($properties)) {
			return;
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

		$this->viewHelperVariableContainer->add('Flowpack\Expose\QueryBehaviors\FilterBehavior', 'filter', $filter);
		$content = $this->viewHelperVariableContainer->getView()->renderPartial('Filter', NULL, array(
			'filter' => $filter,
			'properties' => $properties
		));
		$this->viewHelperVariableContainer->remove('Flowpack\Expose\QueryBehaviors\FilterBehavior', 'filter');
		$this->addToBlock('sidebar', $content);
	}
}

?>