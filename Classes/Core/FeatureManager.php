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
 * FeatureManager to retrieve and Initialize Actions
 *
 * TODO: (SK) the FeatureManager should be fully tested by unit tests or functional tests.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class FeatureManager {

    /**
     * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
     * @FLOW3\Inject
     */
    protected $objectManager;

    /**
     * @var \Foo\ContentManagement\Core\PersistentStorageService
     * @FLOW3\Inject
     */
    protected $persistentStorageService;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
    * TODO: Document this Method! ( getActionByShortName )
    */
    public function getActionByShortName($action = null) {
        if (!stristr($action, 'Action')) {
            $action = $action . 'Action';
        }
        $actions = array(

        );
        foreach ($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\\ContentManagement\\Core\\Actions\\ActionInterface') as $actionClassName) {
            $actionName = $this->persistentStorageService->getShortName($actionClassName);
            if (strtolower($actionName) == strtolower($action)) {
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
        foreach ($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\\ContentManagement\\Core\\Actions\\ActionInterface') as $actionClassName) {
            $actionName = $this->persistentStorageService->getShortName($actionClassName);
            if (strtolower($actionName) == strtolower($action)) {
                return true;
            }
        }
        return false;
    }

    /**
    * TODO: Document this Method! ( getActions )
    */
    public function getActions($action = null, $being = null, $id = false) {
        $actions = array(

        );
        foreach ($this->reflectionService->getAllImplementationClassNamesForInterface('Foo\\ContentManagement\\Core\\Actions\\ActionInterface') as $actionClassName) {
            $inheritingClasses = $this->reflectionService->getAllSubClassNamesForClass($actionClassName);
            foreach ($inheritingClasses as $inheritingClass) {
                $inheritedObject = $this->objectManager->get($actionClassName);
                if ($inheritedObject->override($actionClassName, $being)) {
                    $actionClassName = $inheritedObject;
                }
                unset($inheritedObject);
            }
            $a = $this->objectManager->get($actionClassName);
            foreach ($a->getActionsForContext($being, $action, $id) as $actionName) {
                $actions[] = array(
                	'actionName' => $actionName,
                	'controller' => $a
                );
            }
        }
        ksort($actions);
        return $actions;
    }

    /**
    * TODO: Document this Method! ( getFormRuntime )
    */
    public function getFormRuntime() {
        return $this->formRuntime;
    }

    /**
    * TODO: Document this Method! ( setFormRuntime )
    */
    public function setFormRuntime($formRuntime) {
        $this->formRuntime = $formRuntime;
    }

}

?>