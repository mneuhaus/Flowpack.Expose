<?php
namespace TYPO3\Admin\Core\Features;

/* *
 * This script belongs to the TYPO3.Admin package.              *
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
abstract class AbstractFeature extends \TYPO3\FLOW3\Mvc\Controller\ActionController implements FeatureInterface {

    /**
     * @var \TYPO3\Admin\Core\FeatureManager
     * @FLOW3\Inject
     */
    protected $featureManager;

    /**
     * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
     * @FLOW3\Inject
     */
    protected $persistenceManager;

    /**
    * TODO: Document this Method! ( __toString )
    */
    public function __toString() {
        return $this->getActionName();
    }

    /**
    * TODO: Document this Method! ( getActionName )
    */
    public function getActionName() {
        $action = $this->metaPersistenceManager->getShortName($this);
        return str_replace('Controller', '', $action);
    }

    /**
    * TODO: Document this Method! ( getAction )
    */
    public function getAction() {
        return lcfirst(self::__toString());
    }

    /**
    * TODO: Document this Method! ( getActionsForContext )
    */
    public function getActionsForContext($class, $context, $id) {
        return array();
    }

    /**
    * TODO: Document this Method! ( getClass )
    */
    public function getClass() {
        return 'btn';
    }

    /**
    * TODO: Document this Method! ( getController )
    */
    public function getController() {
        $controller = $this->metaPersistenceManager->getShortName($this);
        return str_replace('Controller', '', $controller);
    }

    /**
    * TODO: Document this Method! ( setFeatureRuntime )
    */
    public function setFeatureRuntime($featureRuntime) {
        $this->featureRuntime = $featureRuntime;
    }

    /**
    * TODO: Document this Method! ( getPackage )
    */
    public function getPackage() {
        return 'TYPO3.Admin';
    }

    /**
    * TODO: Document this Method! ( setRequest )
    */
    public function setRequest($request) {
        $this->request = $request;
    }

    /**
    * TODO: Document this Method! ( getSettings )
    */
    public function getSettings($path = null) {
        $paths = array('TYPO3.Admin.ViewSettings'
        );
        $paths[] = ucfirst($this->getAction());
        if (!is_null($path)) {
            $paths[] = $path;
        }
        return $this->metaPersistenceManager->getSettings(implode('.', $paths));
    }

    /**
    * TODO: Document this Method! ( getShortcut )
    */
    public function getShortcut() {
        return false;
    }

    /**
    * TODO: Document this Method! ( getTarget )
    */
    public function getTarget() {
        return '_self';
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

    /**
    * TODO: Document this Method! ( override )
    */
    public function override($class, $being) {
        return false;
    }

    /**
    * TODO: Document this Method! ( render )
    */
    public function render() {
        $this->initializeView();
        foreach ($this->request->getInternalArgument('__context') as $key => $value) {
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

}

?>