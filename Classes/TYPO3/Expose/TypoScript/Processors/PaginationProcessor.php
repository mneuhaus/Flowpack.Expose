<?php
namespace TYPO3\Expose\TypoScript\Processors;

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
 * Render a Form section using the Form framework
 */
class PaginationProcessor extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {

	/**
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		return $this->tsValue('objects')->getQuery()->setOffset($this->getOffset())->execute();
	}

	/**
	 * @return integer
	 */
	public function getOffset() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		$page = 1;
		if ($request->hasArgument('page')) {
			$page = $request->getArgument('page');
		}

		$offset = $this->getLimit() * ($page - 1);

		$total = $this->tsValue('objects')->count();
		if ($offset > $total) {
			$pages = ceil($total / $this->getLimit());
			$offset = $this->getLimit() * ( $pages - 1 );
		}

		return $offset;
	}

	/**
	 * @return integer
	 */
	public function getLimit() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		if ($request->hasArgument('limit')) {
			return $request->getArgument('limit');
		}

		return (integer)$this->tsValue('defaultLimit<TYPO3.Expose:Settings>/defaultLimit');
	}
}

?>