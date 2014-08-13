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

/**
 */
class SortBehavior extends AbstractQueryBehavior {
	/**
	 *
	 * @param object $query
	 * @return string Rendered string
	 * @api
	 */
	public function run($query) {
		$schema = $this->templateVariableContainer->get('schema');
		$sortBy = $schema->getDefaultSortBy();
		$order = $schema->getDefaultOrder();

		if ($sortBy === NULL) {
			return;
		}

		if( $this->request->hasArgument("sortBy") ){
			$sortBy = $this->request->getArgument("sortBy");

			if( $this->request->hasArgument("order") ){
				$order = $this->request->getArgument("order");
			} else {
				$order = "DESC";
			}
		}

		if ($sortBy !== NULL) {
			$query->setOrderings(array(
				$sortBy => $order
			));
		}

		$this->sorting = array(
			"sortBy" => $sortBy,
			"order"=> $order,
			"oppositeOrder"=> $order == "ASC" ? "DESC" : "ASC"
		);

		$this->addWrapper('field', $this);
	}

	public function wrap($content, $arguments) {
		$arguments['content'] = $content;
		$this->viewHelperVariableContainer->add('Flowpack\Expose\ViewHelpers\Query\SortViewHelper', 'sorting', $this->sorting);
		$content =  $this->viewHelperVariableContainer->getView()->renderPartial('SortField', NULL, $arguments);
		$this->viewHelperVariableContainer->remove('Flowpack\Expose\ViewHelpers\Query\SortViewHelper', 'sorting');
		return $content;
	}
}

?>