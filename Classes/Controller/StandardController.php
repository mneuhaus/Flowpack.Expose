<?php

namespace Foo\ContentManagement\Controller;

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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Standard controller for the Admin package
 *
 * @version $Id: $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class StandardController extends \TYPO3\TYPO3\Controller\Module\StandardController {
	/**
	 * @var \Foo\ContentManagement\Actions\ActionManager
	 * @FLOW3\Inject
	 */
	protected $actionManager;	

	/**
	 * @var \Foo\ContentManagement\Reflection\AnnotationService
	 * @FLOW3\Inject
	 */
	protected $annotationService;

	/**
	 * @var \Foo\ContentManagement\Core\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \Foo\ContentManagement\Adapters\ContentManager
	 * @FLOW3\Inject
	 */
	protected $contentManager;	

	/**
	 * @var \Foo\ContentManagement\Core\Helper
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $helper;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	protected $model = "";
	protected $being = null;
	protected $id = null;
	
	/**
	 * Resolves and checks the current action method name
	 *
	 * @return string Method name of the current action
	 * @author Robert Lemke <robert@typo3.org>
	 */
	protected function resolveActionMethodName() {
		$actionMethodName = $this->request->getControllerActionName() . 'Action';
		if (!$this->actionManager->hasAction($actionMethodName)) {
			throw new \TYPO3\FLOW3\Mvc\Exception\NoSuchActionException('An action "' . $actionMethodName . '" does not exist in controller "' . get_class($this) . '".', 1186669086);
		}
		return $actionMethodName;
	}

	/**
	 * Calls the specified action method and passes the arguments.
	 *
	 * If the action returns a string, it is appended to the content in the
	 * response object. If the action doesn't return anything and a valid
	 * view exists, the view is rendered automatically.
	 *
	 * @param string $actionMethodName Name of the action method to call
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	protected function callActionMethod() {
		$actionResult = $this->__call($this->actionMethodName, $this->request->getArguments());
		
		if ($actionResult === NULL && $this->view instanceof \TYPO3\FLOW3\Mvc\View\ViewInterface) {
			// $cache = $this->cacheManager->getCache('Admin_Cache');
			// $identifier = $this->cacheManager->createIdentifier($this->request->getHttpRequest()->getUri());

			// if(!$cache->has($identifier)){
				$output = $this->view->render();
			// 	$cache->set($identifier, $output);
			// }else{
			// 	$output = $cache->get($identifier);
			// }

			$this->response->appendContent($output);

		} elseif (is_string($actionResult) && strlen($actionResult) > 0) {
			$this->response->appendContent($actionResult);
		} elseif (is_object($actionResult) && method_exists($actionResult, '__toString')) {
			$this->response->appendContent((string)$actionResult);
		}
	}

	public function __call($name, $args){
		$actionName = str_replace("Action","",$name);
		$this->prepare($actionName);
		$action = $this->actionManager->getActionByShortName($name);

		if(!is_object($action))
			parent::redirect("index");

		if($action !== null){
			$ids = explode(",", $this->id);
			$action->execute($this->being, $ids);
		}
	}
	
	public function compileShortNames(){
		$cache = $this->cacheManager->getCache('Admin_Cache');
		$identifier = "ClassShortNames-".sha1(implode("-", array_keys($this->adapters)));

		if(!$cache->has($identifier)){
			$shortNames = array();
			foreach ($this->adapters as $adapter) {
				foreach ($adapter->getGroups() as $group => $beings) {
					foreach ($beings as $conf) {
						$being = $conf["being"];
						$shortName = str_replace("domain_model_", "", strtolower(str_replace("\\", "_", $being)));
						$shortNames[$being] = $shortName;
						$shortNames[$shortName] = $being;
					}
				}
			}
			
			$cache->set($identifier,$shortNames);
		}else{
			$shortNames = $cache->get($identifier);
		}
		
		return $shortNames;
	}

	private function prepare($action){
		$this->adapters = $this->contentManager->getAdapters();
		$this->settings = $this->contentManager->getSettings();
		
		\Foo\ContentManagement\Core\API::set("classShortNames", $this->compileShortNames());

		$mainRequest = $this->request->getMainRequest();
		if($mainRequest->hasArgument($this->request->getArgumentNamespace())){
			$arguments = $mainRequest->getArgument($this->request->getArgumentNamespace());
			$this->request->setArguments($arguments);
				
			#var_dump($arguments);
			if(isset($arguments["being"])){
				$this->being = $arguments["being"];

				if(!stristr($this->being, "\\"))
					$this->being = \Foo\ContentManagement\Core\API::get("classShortNames", $this->being);

				$this->adapter = $this->contentManager->getAdapterByClass($this->being);

				$this->group = $this->contentManager->getGroupByClass($this->being);
			}

			if(isset($arguments["id"])){
				$this->id = $arguments["id"];
				if(is_array($this->id))
					$this->id = implode(",", $this->id);
			}
		}

		$this->actionManager->setRequest($this->request);
		$this->actionManager->setController($this);

		$groups = $this->contentManager->getGroups();
		ksort($groups);
		foreach($groups as $package => $group){
			foreach($group["beings"] as $key => $being){
				
				if(!empty($this->adapter)){
					if($being["being"] == $this->being && $being["adapter"] == $this->adapter){
						$groups[$package]["beings"][$key]["active"] = true;
					}else{
						$groups[$package]["beings"][$key]["active"] = false;
					}
				}
			}
			if(empty($groups[$package]["beings"]))
				unset($groups[$package]);
		}

		$this->view = $this->resolveView();
		$this->view->setTemplateByAction($action);
		$this->actionManager->setView($this->view);
			
		if ($this->view !== NULL) {
			$this->view->assign('settings', $this->settings);
			$this->initializeView($this->view);
		}
		
		$this->view->assign('groups',$groups);
		
		$hasId = isset($this->id) ? true : false;
		$topBarActions = $this->actionManager->getActions($action, $this->being, $hasId);
		$this->view->assign('topBarActions',$topBarActions);
	}

	/**
	 * Determines the fully qualified view object name.
	 *
	 * @return mixed The fully qualified view object name or FALSE if no matching view could be found.
	 * @api
	 */
	protected function resolveViewObjectName() {
		return "Foo\ContentManagement\View\FallbackTemplateView";
	}

	public function getRequest(){
		return $this->request;
	}

	public function getAction(){
		return str_replace("Action","",$this->actionMethodName);
	}

	public function redirect($actionName, $controllerName = NULL, $packageKey = NULL, array $arguments = NULL, $delay = 0, $statusCode = 303, $format = NULL) {
		return parent::redirect($actionName, $controllerName, $packageKey, $arguments, $delay, $statusCode, $format);
	}

	public function forward($actionName, $controllerName = NULL, $packageKey = NULL, array $arguments = NULL) {
		return parent::forward($actionName, $controllerName, $packageKey, $arguments);
	}

	/**
	 * Redirects the web request to another uri.
	 *
	 * NOTE: This method only supports web requests and will throw an exception
	 * if used with other request types.
	 *
	 * @param mixed $uri Either a string representation of a URI or a \TYPO3\FLOW3\Property\DataType\Uri object
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @throws \TYPO3\FLOW3\Mvc\Exception\UnsupportedRequestTypeException If the request is not a web request
	 * @throws \TYPO3\FLOW3\Mvc\Exception\StopActionException
	 * @author Robert Lemke <robert@typo3.org>
	 * @api
	 */
	protected function redirectToUri($uri, $delay = 0, $statusCode = 303) {
#		$uri = $this->request->getBaseUri() . (string)$uri;
		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');
		$this->response->setContent('<html><head><meta http-equiv="refresh" content="' . intval($delay) . ';url=' . $escapedUri . '"/></head></html>');
		$this->response->setStatus($statusCode);
		$this->response->setHeader('Location', (string)$uri);
		throw new \TYPO3\FLOW3\Mvc\Exception\StopActionException();
	}
}

?>