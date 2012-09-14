<?php
namespace TYPO3\Expose\Reflection\Provider;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Configuration provider for the DummyAdapter
 */
class DefaultsAnnotationProvider extends AbstractAnnotationProvider {

	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 * @FLOW3\Inject
	 */
	protected $reflectionService;

	/**
	 * @param string $className
	 * @return array
	 */
	public function getClassAnnotations($className) {
		$defaults = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Defaults');
		$annotations = array();
		foreach ($defaults as $annotationName => $values) {
			if ($annotationName == 'Properties') {
				$propertyDefaults = $values;
				$properties = array_flip($this->reflectionService->getClassPropertyNames($className));
				$annotations['Properties'] = array();
				foreach ($properties as $property => $meta) {
					if ($property == 'FLOW3_Persistence_Identifier') {
						continue;
					}
					$propertyAnnotations = array();
					foreach ($propertyDefaults as $annotationName => $values) {
						$annotationClass = $this->findAnnotationByName($annotationName);
						$values = $this->convert($values);
						$annotation = new $annotationClass($values);
						$this->addAnnotation($propertyAnnotations, $annotation);
					}
					$annotations['Properties'][$property] = $propertyAnnotations;
				}
			} else {
				$annotationClass = $this->findAnnotationByName($annotationName);
				$values = $this->convert($values);
				$annotation = new $annotationClass($values);
				$this->addAnnotation($annotations, $annotation);
			}
		}

		return $annotations;
	}

}

?>