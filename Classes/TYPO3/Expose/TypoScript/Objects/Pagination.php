<?php
namespace TYPO3\Expose\TypoScript\Objects;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class Pagination extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {

	/**
	 * @return string
	 */
	public function evaluate() {
		$currentPage = $this->getCurrentPage();

		$pages = array();
		for ($i = 0; $i < $this->totalObjects() / $this->getLimit(); $i++) {
			$pages[] = $i + 1;
		}

		if ($currentPage > count($pages)) {
			$currentPage = count($pages);
		}

		if (count($pages) > 1) {
			$this->properties['currentPage'] = $currentPage;
			if ($currentPage < count($pages)) {
				$this->properties['nextPage'] = $currentPage + 1;
			}
			if ($currentPage > 1) {
				$this->properties['previousPage'] = $currentPage - 1;
			}
			if (count($pages) > $this->tsValue('<TYPO3.Expose:Pagination>/maxPages')) {
				$max = $this->tsValue('<TYPO3.Expose:Pagination>/maxPages');
				$start = $currentPage - ($max + $max % 2) / 2;
				$start = $start > 0 ? $start : 0;
				$start = $start > 0 ? $start : 0;
				$start = $start + $max > count($pages) ? count($pages) - $max : $start;
				$pages = array_slice($pages, $start, $max);
			}
			$this->properties['pages'] = $pages;
		}

		return parent::evaluate();
	}

	/**
	 * @return integer
	 */
	public function totalObjects() {
		$objects = $this->tsValue('objects');

		if (is_object($objects)) {
			$objects = $objects->getQuery()->setLimit(NULL)->setOffset(NULL)->execute();

			return $objects->count();
		}

		return 0;
	}

	/**
	 * @return integer
	 */
	public function getCurrentPage() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		if ($request->hasArgument('page')) {
			return $request->getArgument('page');
		}

		return 1;
	}

	/**
	 * @return integer
	 */
	public function getLimit() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		if ($request->hasArgument('limit')) {
			return $request->getArgument('limit');
		}

		return (integer)$this->tsValue('<TYPO3.Expose:Limits>/default');
	}
}

?>