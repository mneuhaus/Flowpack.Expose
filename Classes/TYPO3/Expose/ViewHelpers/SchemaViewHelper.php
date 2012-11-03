<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class SchemaViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @param object $object
	 * @param string $className
	 * @param string $as
	 * @return string Rendered string
	 * @api
	 */
	public function render($object = NULL, $className = NULL, $as = 'schema') {
			// TODO: should be retrieved differently
		if (is_null($className) && !is_null($object)) {
			$className = $this->reflectionService->getClassNameByObject($object);
		}

		$fluidTemplateTsObject = $this->templateVariableContainer->get('fluidTemplateTsObject');
		$path = $fluidTemplateTsObject->getPath() . '/<TYPO3.Expose:SchemaLoader>';

		$fluidTemplateTsObject->getTsRuntime()->pushContext('className', $className);
		$schema = $fluidTemplateTsObject->getTsRuntime()->render($path);
		$fluidTemplateTsObject->getTsRuntime()->popContext();

		$this->templateVariableContainer->add($as, $schema);
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		return $output;
	}
}

?>