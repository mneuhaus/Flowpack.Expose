<?php
namespace TYPO3\Expose\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class PaginationPartial extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {

	/**
	 * This is a template method which can be overridden in subclasses to add new variables which should
	 * be available inside the Fluid template. It is needed e.g. for Expose.
	 *
	 * @param \TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view
	 * @return void
	 */
	protected function initializeView(\TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view) {
		$currentPage = $this->getCurrentPage();

		$pages = array();
		for ($i = 0; $i < $this->totalObjects() / $this->getLimit(); $i++) {
			$pages[] = $i + 1;
		}

		if ($currentPage > count($pages)) {
			$currentPage = count($pages);
		}
		$view->assign('currentPage', $currentPage);

		if (count($pages) > 1) {
			if ($currentPage < count($pages)) {
				$view->assign('nextPage', $currentPage + 1);
			}
			if ($currentPage > 1) {
				$view->assign('previousPage', $currentPage - 1);
			}
			$max = $this->tsValue('maxPages<TYPO3.Expose:Settings>/maxPages');
			if (count($pages) > $max) {
				$start = $currentPage - ($max + $max % 2) / 2;
				$start = $start > 0 ? $start : 0;
				$start = $start > 0 ? $start : 0;
				$start = $start + $max > count($pages) ? count($pages) - $max : $start;
				$pages = array_slice($pages, $start, $max);
			}
			$view->assign('pages', $pages);
		}
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

		return (integer)$this->tsValue('defaultLimit<TYPO3.Expose:Settings>/defaultLimit');
	}
}

?>