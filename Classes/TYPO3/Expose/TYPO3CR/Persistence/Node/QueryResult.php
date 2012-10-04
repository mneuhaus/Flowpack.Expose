<?php
namespace TYPO3\Expose\TYPO3CR\Persistence\Node;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A lazy result list that is returned by Query::execute()
 *
 * @api
 */
class QueryResult extends \TYPO3\Flow\Persistence\Doctrine\QueryResult {

	/**
	 * Constructor
	 *
	 * @param \TYPO3\Flow\Persistence\QueryInterface $query
	 */
	public function __construct(\TYPO3\Flow\Persistence\QueryInterface $query) {
		$this->query = $query;
	}

}

?>