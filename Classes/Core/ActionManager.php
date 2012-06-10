<?php

namespace Foo\ContentManagement\Core;

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
 * ActionManager to retrieve and Initialize Actions
 *
 * TODO: (SK) why does the action manager have a reference to $controller $request and view? This should be refactored and moved away from here.
 * TODO: (SK) the ActionManager should be fully tested by unit tests or functional tests.
 *
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
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 * @FLOW3\Inject
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

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
				foreach ($a->getActionsForContext($being, $action, $id) as $actionName) {
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
			$actionName = $this->contentManager->getShortName($actionClassName);
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
			$actionName = $this->contentManager->getShortName($actionClassName);
			if(strtolower($actionName) == strtolower($action)){
				return true;
			}
		}
		return false;
	}

	// TODO: (SK) this redirect duplicates the default functionality of FLOW3
	// 		 (MN) The reason for this was to make this Public because i needed
	// 		      a way to initiate redirects from the AbstractActions
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

	public function setFormRuntime($formRuntime) {
		$this->formRuntime = $formRuntime;
	}

	public function getFormRuntime() {
		return $this->formRuntime;
	}
}
?>