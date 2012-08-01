<?php

namespace Foo\ContentManagement\Core\Actions;

/* *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * abstract base class for the actions
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAction extends \TYPO3\FLOW3\Mvc\Controller\ActionController implements ActionInterface {
	/**
	 * @var \Foo\ContentManagement\Core\FeatureManager
	 */
	protected $featureManager;

	/**
	 * @var \Foo\ContentManagement\Core\PersistentStorageService
	 */
	protected $persistentStorageService;

	/**
	 *
	 * @param \Foo\ContentManagement\Core\PersistentStorageService $persistentStorageService
	 * @param \Foo\ContentManagement\Core\FeatureManager   $featureManager
	 */
	public function __construct(\Foo\ContentManagement\Core\FeatureManager $featureManager, \Foo\ContentManagement\Core\PersistentStorageService $persistentStorageService) {
		$this->featureManager = $featureManager;
		$this->persistentStorageService = $persistentStorageService;
	}

	public function getActionsForContext($class, $context, $id) {
		return array();
	}

	public function getPackage() {
		return "Foo.ContentManagement";
	}

	public function getController() {
		$controller = $this->persistentStorageService->getShortName($this);
		return str_replace("Controller", "", $controller);
	}

	public function getTarget() {
		return "_self";
	}

	public function getClass() {
		return "btn";
	}

	public function __toString() {
		return $this->getActionName();
	}

	public function getActionName() {
		$action = $this->persistentStorageService->getShortName($this);
		return str_replace("Controller", "", $action);
	}

	public function getAction() {
		return lcfirst(self::__toString());
	}

	public function getShortcut(){
		return false;
	}

	public function override($class, $being){
		return false;
	}

	public function render() {
		$this->initializeView();
		
		foreach ($this->request->getInternalArgument("__context") as $key => $value) {
			$this->view->assign($key, $value);
		}

		$actionResult = $this->execute();
		if ($actionResult === NULL && $this->view instanceof \TYPO3\FLOW3\Mvc\View\ViewInterface) {
			return $this->view->render($this->getActionName());
		} elseif (is_string($actionResult) && strlen($actionResult) > 0) {
			return $actionResult;
		} elseif (is_object($actionResult) && method_exists($actionResult, '__toString')) {
			return (string) $actionResult;
		}
	}

#	public function initializeView() {
#		$this->view = new \Foo\ContentManagement\View\FallbackTemplateView();
#		$this->view->setControllerContext($this->actionRuntime->getControllerContext());
#	}

	public function getSettings($path = null){
		$paths = array("Foo.ContentManagement.ViewSettings");
		$paths[] = ucfirst($this->getAction());
		if(!is_null($path))
			$paths[] = $path;
 		return $this->persistentStorageService->getSettings(implode(".", $paths));
	}

	public function setRequest($request) {
		$this->request = $request;
	}

	public function setActionRuntime($actionRuntime) {
		$this->actionRuntime = $actionRuntime;
	}

	/**
	 * Initializes the controller
	 *
	 * This method should be called by the concrete processRequest() method.
	 *
	 * ( I need this function to be public to call it from the ControllerCallbackFinisher )
	 *
	 * @param \TYPO3\FLOW3\Mvc\RequestInterface $request
	 * @param \TYPO3\FLOW3\Mvc\ResponseInterface $response
	 * @throws \TYPO3\FLOW3\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeController(\TYPO3\FLOW3\Mvc\RequestInterface $request, \TYPO3\FLOW3\Mvc\ResponseInterface $response) {
		parent::initializeController($request, $response);
	}
}
?>