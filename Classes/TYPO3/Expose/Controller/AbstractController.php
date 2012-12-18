<?php
namespace TYPO3\Expose\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Base class for expose controllers. An expose controller implements a certain
 * functionality inside the Expose UI, such as "Edit", "New", "List" or "Delete".
 */
abstract class AbstractController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\Expose\\View\\TypoScriptView';

	/**
	 * Most expose controllers need the Property Mapper as they work for arbitrary
	 * data types and need to perform the conversion manually. that's why we inject
	 * it here for convenience reasons.
	 *
	 * @var \TYPO3\Flow\Property\PropertyMapper
	 * @Flow\Inject
	 */
	protected $propertyMapper;

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @param \TYPO3\Flow\Mvc\View\ViewInterface $view The view to be initialized
	 * @return void
	 * @api
	 */
	protected function initializeView(\TYPO3\Flow\Mvc\View\ViewInterface $view) {
		$prefix = '<' . $this->request->getInternalArgument('__typoScriptPrefix') . '>';
		$this->prefixTypoScriptPath($prefix);

		if ($this->request->hasArgument('type')) {
			$type = $this->request->getArgument('type');
			$this->prefixTypoScriptPath('<TYPO3.Expose:Schema:' . str_replace('\\', '.', ltrim($type, '\\')) . '>');
		}
	}

	public function prefixTypoScriptPath($prefix) {
		if ($this->view->getTypoScriptRuntime()->canRender($prefix . '/' . $this->view->getTypoScriptPath())) {
			$this->view->setTypoScriptPath($prefix . '/' . $this->view->getTypoScriptPath());
		}
	}
}

?>