<?php
namespace TYPO3\Expose\Tests\Functional\Actions\Fixtures\Controller;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Controller for simple CRUD actions, to test Fluid forms in
 * combination with Property Mapping
 */
class ActionsController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {

	public function setTemplate($action) {
		$this->view->setTemplatePathAndFilename("Packages/Application/TYPO3.Expose/Tests/Functional/Actions/Fixtures/Templates/" . $action . ".html");
	}

	/**
	 * Prepares a view for the current action and stores it in $this->view.
	 * By default, this method tries to locate a view with a name matching
	 * the current action.
	 *
	 * @return \TYPO3\FLOW3\Mvc\View\ViewInterface the resolved view
	 * @api
	 */
	protected function resolveView() {
		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setControllerContext($this->controllerContext);
		return $view;
	}


	/**
	 * Display a start page
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->setTemplate("Index");
	}

	// /**
	//  * @param \TYPO3\Fluid\Tests\Functional\Form\Fixtures\Domain\Model\Post $post
	//  * @return void
	//  */
	// public function createAction(\TYPO3\Fluid\Tests\Functional\Form\Fixtures\Domain\Model\Post $post) {
	// 	return $post->getName() . '|' . $post->getEmail();
	// }
	// 
	// 
	public function resolveViewObjectName() {
		return $this->defaultViewObjectName;
	}
}
?>