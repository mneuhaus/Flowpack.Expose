<?php
namespace Foo\ContentManagement\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the FLOW3 package "TypoScript".                 *
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
 */
class FormRenderer extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject {

	/**
	 * @var \Foo\ContentManagement\Factory\ModelFormFactory
	 * @FLOW3\Inject
	 */
	protected $formFactory;

	protected $class;

	protected $object;

	protected $controllerCallback;

	public function setClass($class) {
		$this->class = $class;
	}

	public function setObject($object) {
		$this->object = $object;
	}

	public function setControllerCallback($controllerCallback) {
		$this->controllerCallback = $controllerCallback;
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


		$formDefinition = $this->formFactory->build($configuration, 'contentManagement');
		$response = new \TYPO3\FLOW3\Http\Response($this->tsRuntime->getControllerContext()->getResponse());
		$form = $formDefinition->bind($this->tsRuntime->getControllerContext()->getRequest()->getMainRequest(), $response);
		return $form->render();
	}
}
?>