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
class SettingsAnnotationProvider extends AbstractAnnotationProvider {

	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @param string $className
	 * @return array
	 */
	public function getClassAnnotations($className) {
		$classes = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Annotations');
		$annotations = array();
		if (isset($classes[$className])) {
			foreach ($classes[$className] as $annotationName => $values) {
				if ($annotationName == 'Properties') {
					$properties = $values;
					$annotations['Properties'] = array();
					foreach ($properties as $property => $settings) {
						if ($property == 'FLOW3_Persistence_Identifier') {
							continue;
						}
						$propertyAnnotations = array();
						foreach ($settings as $annotationName => $values) {
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
		}

		return $annotations;
	}

}

?>