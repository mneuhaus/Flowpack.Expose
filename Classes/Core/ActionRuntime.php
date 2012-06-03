<?php
namespace Foo\ContentManagement\Core;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * @api
 */
class ActionRuntime {
	/**
	 * @var \Foo\ContentManagement\Core\ActionManager
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
	 * @var \TYPO3\FLOW3\Mvc\ActionRequest
	 * @internal
	 */
	protected $request;

	/**
	 * @var \TYPO3\FLOW3\Http\Response
	 * @internal
	 */
	protected $response;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Security\Cryptography\HashService
	 * @internal
	 */
	protected $hashService;

	/**
	 * Workaround...
	 *
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Mvc\FlashMessageContainer
	 * @internal
	 */
	protected $flashMessageContainer;

	/**
	 * Default action to render if nothing else is specified 
	 * or present in the arguments
	 *
	 * @var string
	 * @internal
	 */
	protected $defaultAction = "index";

	/**
	 * Default being to render if nothing else is specified 
	 * or present in the arguments
	 *
	 * @var string
	 * @internal
	 */
	protected $defaultBeing;

	/**
	 *
	 * @var string
	 * @internal
	 */
	protected $being;

	/**
	 *
	 * @var string
	 */
	protected $namespace = "actionRuntime";

	/**
	 * @param \TYPO3\FLOW3\Mvc\ActionRequest $request
	 * @param \TYPO3\FLOW3\Http\Response $response
	 * @internal
	 */
	public function __construct(\TYPO3\FLOW3\Mvc\ActionRequest $request, \TYPO3\FLOW3\Http\Response $response) {
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * @internal
	 */
	public function initializeObject() {
		$arguments = $this->request->getPluginArguments();
		$this->request = new ActionRequest($this->request);
		$this->request->setArgumentNamespace("--" . $this->namespace);
		if (isset($arguments[$this->namespace])) {
			$this->request->setArguments($arguments[$this->namespace]);
		}
		$this->request->setFormat("html");

		$this->request->setControllerName("ActionRuntime");
		$this->request->setControllerPackageKey("Foo");
		$this->request->setControllerSubpackageKey("ContentManagement");

		$this->initializeActionStateFromRequest();
	}

	/**
	 * @internal
	 */
	protected function initializeActionStateFromRequest() {
		#var_dump($this->request->getArguments());

		if(!is_null($this->request->getControllerActionName()))
			$this->action = $this->request->getControllerActionName();
		else
			$this->action = $this->defaultAction;

	}

	/**
	 *
	 * @return string rendered form
	 * @api
	 */
	public function execute() {
		$content = "";
		$dispatchLoopCount = 0;
		$request = $this->request;
		while (!$request->isDispatched()) {
			if ($dispatchLoopCount++ > 99) {
				throw new \TYPO3\FLOW3\Mvc\Exception\InfiniteLoopException('Could not ultimately dispatch the request after '  . $dispatchLoopCount . ' iterations.', 1217839467);
			}
			try{
				$action = $this->actionManager->getActionByShortName($request->getControllerActionName() ? $request->getControllerActionName() : $this->defaultAction);
				$action->setRequest($request);
				$action->setActionRuntime($this);
				$content = $action->render();
				$request->setDispatched(true);
			}catch (\TYPO3\FLOW3\Mvc\Exception\ForwardException $exception){
				$request = $exception->getNextRequest();
			}
		}
		return $content;
	}

	/**
	 * Get the request this object is bound to.
	 *
	 * This is mostly relevant inside Finishers, where you f.e. want to redirect
	 * the user to another page.
	 *
	 * @return \TYPO3\FLOW3\Mvc\ActionRequest the request this object is bound to
	 * @api
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @param \TYPO3\FLOW3\Mvc\ActionRequest $request
	 */
	public function setRequest(\TYPO3\FLOW3\Mvc\ActionRequest $request) {
		$this->request = $request;
	}

	/**
	 * Get the response this object is bound to.
	 *
	 * This is mostly relevant inside Finishers, where you f.e. want to set response
	 * headers or output content.
	 *
	 * @return \TYPO3\FLOW3\Http\Response the response this object is bound to
	 * @api
	 */
	public function getResponse() {
		return $this->response;
	}
	
	/**
	 * @param \TYPO3\FLOW3\Http\Response $response
	 */
	public function setResponse(\TYPO3\FLOW3\Http\Response $response) {
		$this->response = $response;
	}

	/**
	 * @return \TYPO3\FLOW3\Mvc\Controller\ControllerContext
	 */
	public function getControllerContext() {
		$uriBuilder = new \TYPO3\FLOW3\Mvc\Routing\UriBuilder();
		$uriBuilder->setRequest($this->request);

		return new \TYPO3\FLOW3\Mvc\Controller\ControllerContext(
			$this->request,
			$this->response,
			new \TYPO3\FLOW3\Mvc\Controller\Arguments(array()),
			$uriBuilder,
			$this->flashMessageContainer
		);
	}

	public function setDefaultAction($action) {
		$this->defaultAction = $action;
	}

	public function setDefaultBeing($being) {
		$this->defaultBeing = $being;
	}
}
?>