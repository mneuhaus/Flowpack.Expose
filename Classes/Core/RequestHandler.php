<?php

namespace Foo\ContentManagement\Core;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
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
 * Class to enable Redirecting and forwarding outside of the ControllerClass
 * 
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class RequestHandler {
    /**
     * The current action request directed to this controller
     * @var \TYPO3\FLOW3\Mvc\ActionRequest
     * @api
     */
    protected $request;

    /**
     * @FLOW3\Inject
     * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

    public function __construct($request) {
        $this->request = $request;
    }

    public function redirect($actionName, $arguments = array(), $delay = 0, $statusCode = 303) {
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

    /**
     * Forwards the request to another action and / or controller.
     *
     * Request is directly transfered to the other action / controller
     *
     * @param string $actionName Name of the action to forward to
     * @param string $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
     * @param string $packageKey Key of the package containing the controller to forward to. May also contain the sub package, concatenated with backslash (Vendor.Foo\Bar\Baz). If not specified, the current package is assumed.
     * @param array $arguments Arguments to pass to the target action
     * @return void
     * @throws \TYPO3\FLOW3\Mvc\Exception\ForwardException
     * @see redirect()
     * @api
     */
    public function forward($actionName, $controllerName = NULL, $packageKey = NULL, array $arguments = array()) {
        $nextRequest = clone $this->request;
        $nextRequest->setControllerActionName($actionName);

        if ($controllerName !== NULL) {
            $nextRequest->setControllerName($controllerName);
        }
        if ($packageKey !== NULL && strpos($packageKey, '\\') !== FALSE) {
            list($packageKey, $subpackageKey) = explode('\\', $packageKey, 2);
        } else {
            $subpackageKey = NULL;
        }
        if ($packageKey !== NULL) {
            $nextRequest->setControllerPackageKey($packageKey);
        }
        if ($subpackageKey !== NULL) {
            $nextRequest->setControllerSubpackageKey($subpackageKey);
        }

        $regularArguments = array();
        foreach ($arguments as $argumentName => $argumentValue) {
            if (substr($argumentName, 0, 2) === '__') {
                $nextRequest->setArgument($argumentName, $argumentValue);
            } else {
                $regularArguments[$argumentName] = $argumentValue;
            }
        }
        $nextRequest->setArguments($this->persistenceManager->convertObjectsToIdentityArrays($regularArguments));

        $forwardException = new \TYPO3\FLOW3\Mvc\Exception\ForwardException();
        $forwardException->setNextRequest($nextRequest);
        throw $forwardException;
    }
}

?>