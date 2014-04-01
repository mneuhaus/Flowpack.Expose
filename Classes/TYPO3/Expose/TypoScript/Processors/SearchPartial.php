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
class SearchPartial extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {

	/**
	 * This is a template method which can be overridden in subclasses to add new variables which should
	 * be available inside the Fluid template. It is needed e.g. for Expose.
	 *
	 * @param \TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view
	 * @return void
	 */
	protected function initializeView(\TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view) {
		$view->assign('search', $this->getSearch());
	}

	/**
	 * @return string
	 */
	public function getSearch() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		if ($request->hasArgument('search')) {
			return $request->getArgument('search');
		}
		return NULL;
	}
}

?>