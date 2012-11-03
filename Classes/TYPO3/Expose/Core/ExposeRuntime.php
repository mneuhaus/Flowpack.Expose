<?php
namespace TYPO3\Expose\Core;

/* *
 * This script belongs to the TYPO3.Expose package.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;

/**
 */
class ExposeRuntime {

	/**
	 * Default action to render if nothing else is specified
	 * or present in the arguments
	 *
	 * @var string
	 */
	protected $defaultAction = 'index';

	/**
	 * Default controller to render if nothing else is specified
	 * or present in the arguments
	 *
	 * @var string
	 */
	protected $defaultController = 'Index';

	/**
	 * Default package to render if nothing else is specified
	 * or present in the arguments
	 *
	 * @var string
	 */
	protected $defaultPackage = 'TYPO3.Expose';

	/**
	 * @var array
	 */
	protected $defaultArguments = array();

	/**
	 * @var \TYPO3\Flow\Mvc\Dispatcher
	 * @Flow\Inject
	 */
	protected $dispatcher;

	/**
	 * @var string
	 */
	protected $namespace = 'exposeRuntime';

	/**
	 * @var \TYPO3\Flow\Mvc\ActionRequest
	 */
	protected $request;

	/**
	 * @var \TYPO3\Flow\Http\Response
	 */
	protected $response;

	/**
	 * @param \TYPO3\Flow\Mvc\ActionRequest $parentRequest
	 * @param \TYPO3\Flow\Http\Response $response
	 */
	public function __construct(\TYPO3\Flow\Mvc\ActionRequest $parentRequest, \TYPO3\Flow\Http\Response $response) {
		$arguments = $parentRequest->getPluginArguments();
		$this->request = new ActionRequest($parentRequest);
		$this->request->setArgumentNamespace('--' . $this->namespace);
		$this->request->setControllerActionName($this->defaultAction);
		$this->request->setControllerPackageKey($this->defaultPackage);
		if (isset($arguments[$this->namespace])) {
			$this->request->setArguments($arguments[$this->namespace]);
		}

		$this->request->setFormat('html');
			// TODO: the response below should be an MVC response
		$this->response = new \TYPO3\Flow\Http\Response($response);
	}

	/**
	 * This method is a workaround for URI building in case of the first request.
	 * Normally, request arguments are not expected to be modified by the user;
	 * and uri building relies on the fact that inside the main request, all nested
	 * arguments are there. This is especially important when addQueryString=TRUE.
	 * In case of the configured default arguments, we need to set them in the parent requests
	 * all up to the main request, such that they are caught by the URI builder when
	 * addQueryString=TRUE.
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $request
	 * @param array $argumentValueToSet
	 * @return void
	 */
	protected function setArgumentInParentRequests(ActionRequest $request, array $argumentValueToSet) {
		if ($request->getMainRequest() === $request) {
			return;
		}
		$parentRequest = $request->getParentRequest();
		$currentNamespace = $request->getArgumentNamespace();
		$parentRequest->setArgument($currentNamespace, $argumentValueToSet);
		$this->setArgumentInParentRequests($parentRequest, array($currentNamespace => $argumentValueToSet));
	}

	/**
	 * Set the arguments for the initial expose controller
	 *
	 * @param array $defaultArguments
	 * @return void
	 */
	public function setDefaultArguments(array $defaultArguments) {
		$this->defaultArguments = $defaultArguments;
	}

	/**
	 * Set the action to be used when no other
	 * action has been specified
	 *
	 * @param string $defaultAction
	 * @return void
	 */
	public function setDefaultAction($defaultAction) {
		$this->defaultAction = $defaultAction;
	}

	/**
	 * Set the controller to be used when no other
	 * expose controller has been specified
	 *
	 * @param string $defaultController
	 * @return void
	 */
	public function setDefaultController($defaultController) {
		$this->defaultController = $defaultController;
	}

	/**
	 * Set the package to be used when no other
	 * expose package has been specified
	 *
	 * @param string $defaultPackage
	 * @return void
	 */
	public function setDefaultPackage($defaultPackage) {
		$this->defaultPackage = $defaultPackage;
	}

	/**
	 * @return string
	 */
	public function execute() {
		$this->prepareExecution();
		$this->dispatcher->dispatch($this->request, $this->response);

		return $this->response->getContent();
	}

	/**
	 * @return void
	 */
	protected function prepareExecution() {
		$controllerObjectName = $this->request->getControllerObjectName();
		if (empty($controllerObjectName)) {
			$this->request->setControllerActionName($this->defaultAction);
			$this->request->setControllerName($this->defaultController);
			$this->request->setControllerPackageKey($this->defaultPackage);
			$this->request->setArguments($this->defaultArguments);
			$this->setArgumentInParentRequests($this->request, $this->defaultArguments);
		}
		if ($this->request->getControllerActionName() === NULL) {
			$this->request->setControllerActionName($this->defaultAction);
		}
	}

	/**
	 * Set a prefix for the Controllers
	 *
	 * @param string $prefix
	 */
	public function setTypoScriptPrefix($prefix) {
		$this->request->setArgument('__typoScriptPrefix', $prefix);
	}
}

?>