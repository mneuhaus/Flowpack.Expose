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
class NavigationViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param string $action name of the entry action
     * @param string $controller name of the entry controller
     * @param string $navigationProvider the class to render the form for
     * @param array $options the object to rende the form for
     * @param string $navigationListElement TypoScript Element to use to render the ListElement
     * @param array $context
     * @return string the rendered form
     */
    public function render($action = null, $controller = null, $navigationProvider = null, $options = array(), $navigationListElement = null, $context = array()) {
        $response = new \TYPO3\FLOW3\Http\Response($this->controllerContext->getResponse());
        $request = $this->controllerContext->getRequest();
        $navigationRuntime = new \TYPO3\Expose\Core\NavigationRuntime($request, $response);
        if (!is_null($action)) {
            $navigationRuntime->setDefaultAction($action);
        }
        if (!is_null($controller)) {
            $navigationRuntime->setDefaultController($controller);
        }
        if (!is_null($navigationListElement)) {
            $navigationRuntime->setNavigationListElement($navigationListElement);
        }
        if (!empty($context)) {
            $navigationRuntime->setContext($context);
        }
        return $navigationRuntime->execute();
    }

}

?>