<?php
namespace TYPO3\Expose\TypoScript\Objects\Backend;

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
use TYPO3\Flow\Utility\PositionalArraySorter;

/**
 * Render a Form section using the Form framework
 */
class NavigationItem extends \TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation {
	/**
	 * This is a template method which can be overridden in subclasses to add new variables which should
	 * be available inside the Fluid template. It is needed e.g. for Expose.
	 *
	 * @param Helpers\FluidView $view
	 * @return void
	 */
	protected function initializeView(\TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView $view) {
		if ($this->tsValue('useSubrequest')) {

		}
		// $view->assign('items', $output);
	}

	/**
	 * Sort the TypoScript objects inside $this->subElements depending on:
	 * - numerical ordering
	 * - position meta-property
	 *
	 * @see \TYPO3\Flow\Utility\PositionalArraySorter
	 * TODO Detect circles in after / before dependencies
	 *
	 * @return array an ordered list of keys
	 * @throws TypoScript\Exception if the positional string has an unsupported format
	 */
	protected function sortNestedTypoScriptKeys() {
		$arraySorter = new PositionalArraySorter($this->properties, '__meta.position');
		try {
			$sortedTypoScriptKeys = $arraySorter->getSortedKeys();
		} catch (InvalidPositionException $exception) {
			throw new TypoScript\Exception('Invalid position string', 1345126502, $exception);
		}
		return $sortedTypoScriptKeys;
	}
}

?>