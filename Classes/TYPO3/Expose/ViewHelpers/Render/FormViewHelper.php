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
 * Render a Form for an Object or class
 *
 * Usage
 * =====
 *
 * <pre>
 * {namespace e=TYPO3\Expose\ViewHelpers}
 * <e:render.form class="Foo\Domain\Model\Bar" />
 * </pre>
 *
 * The factory class must implement {@link TYPO3\Form\Factory\FormFactoryInterface}.
 *
 * @api
 */
class FormViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $presetName name of the preset to use
	 * @param string $class the class to render the form for
	 * @param object $object the object to rende the form for
	 * @param string $callbackAction action to redirect the successful form to
	 * @return string the rendered form
	 */
	public function render($presetName = 'expose', $class = NULL, $object = NULL, $callbackAction = NULL) {
		if (!is_null($class)) {
			$object = new $class();
		}

		if (!is_null($object)) {
			$class = get_class($object);
		}

		$variables = array(
			'className' => $class,
			'objects' => array($object),
			'callbackAction' => $callbackAction
		);

		$renderer = new \TYPO3\Expose\TypoScript\Renderer();
		$renderer->setPackageKey('TYPO3.Expose');

		return $renderer->renderPath('<TYPO3.Expose:FormLayout>', $variables, $this->controllerContext);
	}

}

?>