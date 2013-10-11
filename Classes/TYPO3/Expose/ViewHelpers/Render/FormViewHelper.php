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

use TYPO3\Flow\Annotations as Flow;

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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * The reflectionService
	 *
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @param string $presetName name of the preset to use
	 * @param string $class the class to render the form for
	 * @param object $object the object to rende the form for
	 * @param string $callbackAction action to redirect the successful form to
	 * @param string $typoScriptPrefix prototype prefix for the TypoScript rendering
	 * @return string the rendered form
	 */
	public function render($presetName = 'expose', $class = NULL, $object = NULL, $callbackAction = NULL, $typoScriptPrefix = NULL) {
		if (!is_null($class)) {
			$object = new $class();
		}

		if (!is_null($object)) {
			$class = $this->reflectionService->getClassNameByObject($object);
		}

		$view = new \TYPO3\TypoScript\View\TypoScriptView();
		$view->setPackageKey('TYPO3.Expose');
		$view->setControllerContext($this->controllerContext);
		$view->assignMultiple(array(
			'className' => $class,
			'objects' => array($object),
			'callbackAction' => $callbackAction
		));

		$configuration = $this->configurationManager->getConfiguration('Views', 'TYPO3.Expose');
		$view->setTypoScriptPathPatterns($configuration['options']['typoScriptPathPatterns']);

		$path = '<TYPO3.Expose:FormController>';

		$customPath = '<' . $typoScriptPrefix . '>/' . $path;
		if ($view->getTypoScriptRuntime()->canRender($customPath)) {
			$path = $customPath;
		}

		$schemaPath = '<TYPO3.Expose:Schema:' . str_replace('\\', '.', $class) . '>/' . $path;
		if ($view->getTypoScriptRuntime()->canRender($schemaPath)) {
			$path = $schemaPath;
		}

		$view->setTypoScriptPath($path);
		return $view->render();
	}
}

?>