<?php

namespace TYPO3\Expose\Core;

/* *
 * This script belongs to the TYPO3.Expose package.              *
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
 *
 * // REVIEWED for release.
 */
class AbstractRuntime {

	/**
	 * @var array
	 */
	protected $defaultExposeControllerArguments = array();

	/**
	 * Default controller to render if nothing else is specified
	 * or present in the arguments
	 *
	 * @var string
	 * @internal
	 */
	protected $defaultExposeControllerClassName = 'FILL_IN_DEFAULT_CONTROLLER_CLASS';

	/**
	 * @var \TYPO3\FLOW3\Mvc\Dispatcher
	 * @FLOW3\Inject
	 */
	protected $dispatcher;

	/**
	 * @var string
	 */
	protected $namespace = 'FILL_IN_NAMESPACE';

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
	 * @param \TYPO3\FLOW3\Mvc\ActionRequest $parentRequest
	 * @param \TYPO3\FLOW3\Http\Response $response
	 * @internal
	 */
	public function __construct(\TYPO3\FLOW3\Mvc\ActionRequest $parentRequest, \TYPO3\FLOW3\Http\Response $response) {
		$arguments = $parentRequest->getPluginArguments();
		$this->request = new ActionRequest($parentRequest);
		$this->request->setArgumentNamespace('--' . $this->namespace);
		if (isset($arguments[$this->namespace])) {
			$this->request->setArguments($arguments[$this->namespace]);
		}

		if ($parentRequest->hasArgument('hideModuleDecoration')) {
			$response->setHeader('X-TYPO3-Hide-Module-Decoration', '1');
		}

		$this->request->setFormat('html');
		// TODO: the response below should be an MVC response
		$this->response = new \TYPO3\FLOW3\Http\Response($response);
	}

	/**
	 * This method is a workaround for URI building in case of the first request.
	 *
	 * Normally, request arguments are not expected to be modified by the user;
	 * and uri building relies on the fact that inside the main request, all nested
	 * arguments are there. This is especially important when addQueryString=TRUE.
	 *
	 * In case of the configured default arguments, we need to set them in the parent requests
	 * all up to the main request, such that they are caught by the URI builder when
	 * addQueryString=TRUE.
	 *
	 * @param \TYPO3\FLOW3\Mvc\ActionRequest $request
	 * @param array $argumentToSet
	 * @return oid
	 */
	protected function setArgumentInParentRequests(ActionRequest $request, array $argumentToSet) {
		if ($request->getMainRequest() === $request) {
			return;
		}
		$parentRequest = $request->getParentRequest();
		$currentNamespace = $request->getArgumentNamespace();
		$parentRequest->setArgument($currentNamespace, $argumentToSet);
		$this->setArgumentInParentRequests($parentRequest, array($currentNamespace => $argumentToSet));
	}

	/**
	 * Set the arguments for the initial expose controller
	 *
	 * @param array $defaultExposeControllerArguments
	 */
	public function setDefaultExposeControllerArguments(array $defaultExposeControllerArguments) {
		$this->defaultExposeControllerArguments = $defaultExposeControllerArguments;
	}

	/**
	 * Set the class name for the expose controller to be used when no other
	 * expose controller has been specified
	 *
	 * @param string $defaultExposeControllerClassName
	 * @return void
	 */
	public function setDefaultExposeContollerClassName($defaultExposeControllerClassName) {
		$this->defaultExposeControllerClassName = $defaultExposeControllerClassName;
	}

	/**
	 *
	 * @api
	 */
	public function execute() {
		$this->prepareExecution();
		$this->dispatcher->dispatch($this->request, $this->response);

		return $this->response->getContent();
	}

	/**
	 * TODO: Document this Method! ( prepareExecution )
	 */
	protected function prepareExecution() {
		$controllerObjectName = $this->request->getControllerObjectName();
		if (empty($controllerObjectName)) {
			$this->request->setControllerObjectName($this->defaultExposeControllerClassName);
			$this->request->setArguments($this->defaultExposeControllerArguments);
			$this->setArgumentInParentRequests($this->request, $this->defaultExposeControllerArguments);
		}
		if ($this->request->getControllerActionName() === NULL) {
			$this->request->setControllerActionName('index');
		}
	}

}

?>