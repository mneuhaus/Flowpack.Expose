<?php
namespace Foo\ContentManagement\ViewHelpers\Render;

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

/**
 * Render a Form for an Object or class
 *
 * Usage
 * =====
 *
 * <pre>
 * {namespace cm=Foo\ContentManagement\ViewHelpers}
 * <cm:render.form factoryClass="NameOfYourCustomFactoryClass" />
 * </pre>
 *
 * The factory class must implement {@link TYPO3\Form\Factory\FormFactoryInterface}.
 *
 * @api
 */
class FormViewHelper extends \TYPO3\Form\ViewHelpers\RenderViewHelper {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Form\Persistence\FormPersistenceManagerInterface
	 */
	protected $formPersistenceManager;

	/**
	 * @param string $persistenceIdentifier the persistence identifier for the form.
	 * @param string $factoryClass The fully qualified class name of the factory (which has to implement \TYPO3\Form\Factory\FormFactoryInterface)
	 * @param string $presetName name of the preset to use
	 * @param array $overrideConfiguration factory specific configuration
	 * @param class $class the class to render the form for
	 * @param object $object the object to rende the form for
	 * @return string the rendered form
	 */
	public function render($persistenceIdentifier = NULL, $factoryClass = 'Foo\ContentManagement\Factory\ModelFormFactory', $presetName = 'default', array $overrideConfiguration = array(), $class = NULL, $object = NULL) {
		if (isset($persistenceIdentifier)) {
			$overrideConfiguration = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($this->formPersistenceManager->load($persistenceIdentifier), $overrideConfiguration);
		}

		if(!is_null($class))
			$overrideConfiguration["class"] = $class;

		if(is_object($object))
			$overrideConfiguration["object"] = $object;

		$factory = $this->objectManager->get($factoryClass);
		$factory->setRequest($this->controllerContext->getRequest());
		$formDefinition = $factory->build($overrideConfiguration, $presetName);
		$response = new \TYPO3\FLOW3\Mvc\Response($this->controllerContext->getResponse());

		$form = $formDefinition->bind($this->controllerContext->getRequest(), $response);
		return $form->render();
	}
}
?>