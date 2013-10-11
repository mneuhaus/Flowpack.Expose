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
 */
class LimitPartial extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {

	/**
	 * This is a template method which can be overridden in subclasses to add new variables which should
	 * be available inside the Fluid template. It is needed e.g. for Expose.
	 *
	 * @param Helpers\FluidView $view
	 * @return void
	 */
	protected function initializeView(\TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view) {
		$limits = array();
		foreach ($this->tsValue('<TYPO3.Expose:Settings>/limits') as $limit) {
			$limits[$limit] = FALSE;
		}
		$limit = $this->getLimit();
		$total = $this->totalObjects();

		$unset = FALSE;
		foreach ($limits as $key => $value) {
			$limits[$key] = $limit == $key;
			if (!$unset && intval($key) >= $total) {
				$unset = TRUE;
				continue;
			}
			if ($unset) {
				unset($limits[$key]);
			}
		}

		if (count($limits) === 1) {
			$limits = array();
		}

		$view->assign('limits', $limits);
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