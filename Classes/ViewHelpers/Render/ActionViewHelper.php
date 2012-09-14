<?php
namespace TYPO3\Expose\ViewHelpers\Render;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Render a Action for an Object or class
 *
 * The factory class must implement {@link TYPO3\Form\Factory\FormFactoryInterface}.
 *
 * @api
 */
class ActionViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param string $action name of the entry action
     * @param string $controller name of the entry controller
     * @param string $class the class to render the form for
     * @param object $object the object to rende the form for
     * @param array $overrideConfiguration factory specific configuration
     * @param array $context
     * @return string the rendered form
     */
    public function render($action = 'index', $controller = 'TYPO3\\Expose\\Controller\\IndexController', $class = NULL, $object = NULL, array $overrideConfiguration = array(), $context = null) {
        $response = new \TYPO3\FLOW3\Http\Response($this->controllerContext->getResponse());
        $request = $this->controllerContext->getRequest();
        $featureRuntime = new \TYPO3\Expose\Core\FeatureRuntime($request, $response);
        if (!is_null($class)) {
            $featureRuntime->setDefaultBeing($class);
        }
        $featureRuntime->setDefaultAction($action);
        $featureRuntime->setDefaultController($controller);
        if (!is_null($context)) {
            $featureRuntime->setContext($context);
        }
        return $featureRuntime->execute();
    }

}

?>