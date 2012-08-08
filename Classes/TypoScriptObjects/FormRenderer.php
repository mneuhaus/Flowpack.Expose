<?php
namespace TYPO3\Admin\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Render a Form using the Form framework
 *
 * // REVIEWED for release
 */
class FormRenderer extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject {

    /**
    * TODO: Document this Property!
    */
    protected $class;

    /**
    * TODO: Document this Property!
    */
    protected $controllerCallback;

    /**
     * @var \TYPO3\Admin\Factory\ModelFormFactory
     * @FLOW3\Inject
     */
    protected $formFactory;

    /**
    * TODO: Document this Property!
    */
    protected $object;

    /**
    * TODO: Document this Method! ( setClass )
    */
    public function setClass($class) {
        $this->class = $class;
    }

    /**
    * TODO: Document this Method! ( setControllerCallback )
    */
    public function setControllerCallback($controllerCallback) {
        $this->controllerCallback = $controllerCallback;
    }

    /**
    * TODO: Document this Method! ( setObject )
    */
    public function setObject($object) {
        $this->object = $object;
    }

    /**
     * Evaluate the collection nodes
     *
     * @param mixed $context
     * @return string
     */
    public function evaluate($context) {
        $configuration = array();
        $class = $this->tsValue('class');
        if ($class !== NULL) {
            $configuration['class'] = $class;
        }
        $object = $this->tsValue('object');
        if (is_object($object)) {
            $configuration['object'] = $object;
        }
        $controllerCallback = $this->tsValue('controllerCallback');
        if ($controllerCallback !== NULL) {
            $configuration['controllerCallback'] = $controllerCallback;
        }
        $formDefinition = $this->formFactory->build($configuration, 'admin');
        $response = new \TYPO3\FLOW3\Http\Response($this->tsRuntime->getControllerContext()->getResponse());
        $form = $formDefinition->bind($this->tsRuntime->getControllerContext()->getRequest()->getMainRequest(), $response);
        return $form->render();
    }

}

?>