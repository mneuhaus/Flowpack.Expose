<?php

namespace Foo\ContentManagement\Actions;

/* *
 * This script belongs to the FLOW3 framework.                            *
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
 * ActionManager to retrieve and Initialize Actions
 *
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class ActionManager {
	/**
	 * @var \Foo\ContentManagement\Adapters\ContentManager
	 * @FLOW3\Inject
	 */
	protected $contentManager;	

	/**
	 *
	 * @var object
	 **/
	protected $controller;

	/**
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * The current action request directed to the controller
	 * @var \TYPO3\FLOW3\Mvc\ActionRequest
	 */
	protected $request;

	/**
	 * The current view directed to the controller
	 * @var \TYPO3\FLOW3\Mvc\View\ViewInterface
	 */
	protected $view;

	public function getActions($action = null, $being = null, $id = false){
#		$cache = $this->cacheManager->getCache('Admin_ActionCache');
#		$identifier = sha1($action.$being.$id.$this->adapter);

#		if(!$cache->has($identifier) && false){
			$actions = array();
			foreach($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\ContentManagement\Core\Actions\ActionInterface') as $actionClassName) {
				$inheritingClasses = $this->reflectionService->getAllSubClassNamesForClass($actionClassName);
				foreach($inheritingClasses as $inheritingClass){
					$inheritedObject = $this->objectManager->get($actionClassName);
					if($inheritedObject->override($actionClassName,$being)){
						$actionClassName = $inheritedObject;
					}
					unset($inheritedObject);
				}
				
				$a = $this->objectManager->get($actionClassName);
				if($a->canHandle($being, $action, $id)){
					// TODO: Remove Helper
					$actionName = \Foo\ContentManagement\Core\Helper::getShortName($actionClassName);
					$actionName = str_replace("Action","",$actionName);
					$actions[$actionName] = $a;
				}
			}
			ksort($actions);
			#$cache->set($identifier,$actions);
#		}else{
#			$actions = $cache->get($identifier);
#		}
		
		return $actions;
	}

	public function getActionByShortName($action = null){
		if(!stristr($action, "Action"))
			$action = $action."Action";
		$actions = array();
		foreach($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\ContentManagement\Core\Actions\ActionInterface') as $actionClassName) {
			// TODO: Remove Helper
			$actionName = \Foo\ContentManagement\Core\Helper::getShortName($actionClassName);
			if(strtolower($actionName) == strtolower($action)){
				return $this->objectManager->get($actionClassName);
			}
		}
		return null;
	}

	/**
	 * 
	 * @param  string  $action
	 * @return boolean
	 */
	public function hasAction($action) {
		foreach($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\ContentManagement\Core\Actions\ActionInterface') as $actionClassName) {
			// TODO: Remove Helper
			$actionName = \Foo\ContentManagement\Core\Helper::getShortName($actionClassName);
			if(strtolower($actionName) == strtolower($action)){
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the request
	 * 
	 * @return \TYPO3\FLOW3\Mvc\ActionRequest $request 
	 */
	public function getRequest() {
		return $this->request;
	}


	/**
	 * Set the request
	 * 
	 * @param \TYPO3\FLOW3\Mvc\ActionRequest $request 
	 */
	public function setRequest(\TYPO3\FLOW3\Mvc\ActionRequest $request) {
		$this->request = $request;
	}

	public function setController($controller) {
		$this->controller = $controller;
	}

	public function getController() {
		return $this->controller;
	}

	/**
	 * Get the view
	 * 
	 * @return \TYPO3\FLOW3\Mvc\View\ViewInterface $view 
	 */
	public function getView() {
		return $this->view;
	}	

	/**
	 * Set the view
	 * 
	 * @param \TYPO3\FLOW3\Mvc\View\ViewInterface $view 
	 */
	public function setView(\TYPO3\FLOW3\Mvc\View\ViewInterface &$view) {
		$this->view = $view;
	}

	public function redirect($actionName, $arguments, $delay = 0, $statusCode = 303) {
		$uriBuilder = new \TYPO3\FLOW3\Mvc\Routing\UriBuilder();
		$uriBuilder->setRequest($this->request);
		$uriBuilder->reset();
		
		$uri = $uriBuilder->uriFor($actionName, $arguments);
		$uri = $this->request->getHttpRequest()->getBaseUri() . $uri;

		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');
		$response = new \TYPO3\FLOW3\Http\Response();
		$response->setContent('<html><head><meta http-equiv="refresh" content="' . $delay . ';url=' . $escapedUri . '"/></head></html>');
		$response->setStatus($statusCode);
		if ($delay === 0) {
			$response->setHeader('Location', (string)$uri);
		}
		$response->send();
		throw new \TYPO3\FLOW3\Mvc\Exception\StopActionException();
	}
}
?>