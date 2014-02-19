<?php
namespace TYPO3\Expose\Neos;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Neos".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Neos\Service\PluginService;
use TYPO3\TYPO3CR\Domain\Model\NodeType;
use TYPO3\TYPO3CR\NodeTypePostprocessor\NodeTypePostprocessorInterface;

/**
 * This Processor updates the PluginViews NodeType with the existing
 * Plugins and it's corresponding available Views
 */
class ExposeNodeTypePostprocessor implements NodeTypePostprocessorInterface {

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * Returns the processed Configuration
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeType $nodeType (uninitialized) The node type to process
	 * @param array $configuration input configuration
	 * @param array $options The processor options
	 * @return void
	 */
	public function process(NodeType $nodeType, array &$configuration, array $options) {

		$configuration['properties']['controller']['ui']['inspector']['editorOptions']['values'] = $this->getControllers();
		$configuration['properties']['type']['ui']['inspector']['editorOptions']['values'] = $this->getTypes();
	}

	public function getControllers() {
		$exposeControllers = $this->reflectionService->getAllSubClassNamesForClass('\TYPO3\Expose\Controller\AbstractController');

		$values = array('');
		foreach ($exposeControllers as $exposeController) {
			preg_match('/
				^(
					Controller
				|
					(?P<packageKey>.+)\\\\Controller
				)
				\\\\(?P<controllerName>[a-z\\\\]+)Controller
				$/ix', $exposeController, $matches
			);
			$values[$matches['controllerName']] = array(
				'label' => $matches['packageKey'] . ': ' . $matches['controllerName']
			);
		}
		return $values;
	}

	public function getTypes() {
		$types = $this->reflectionService->getClassNamesByAnnotation('\TYPO3\Flow\Annotations\Entity');

		$values = array('');
		foreach ($types as $type) {
			$values[$type] = array(
				'label' => $this->getClassLabel($type)
			);
		}
		return $values;
	}

	public function getClassLabel($className) {
		preg_match('/([^\\\\]*\\\\[^\\\\]*)/', $className, $match);
		$packageName = $match[1];
		preg_match('/([^\\\\]*$)/', $className, $match);
		$entityName = $match[1];
		return $packageName . ': ' . $entityName;
	}
}
