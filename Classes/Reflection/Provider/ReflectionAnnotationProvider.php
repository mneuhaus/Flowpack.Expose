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
class ReflectionAnnotationProvider extends AbstractAnnotationProvider {

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
		$annotations = array();
		foreach ($this->reflectionService->getClassAnnotations($className) as $annotation) {
			$this->addAnnotation($annotations, $annotation);
		}
		$annotations['Properties'] = array();
		foreach ($this->reflectionService->getClassPropertyNames($className) as $property) {
			$propertyAnnotations = array();
			foreach ($this->reflectionService->getPropertyAnnotations($className, $property) as $annotation) {
				$this->addAnnotation($propertyAnnotations, $annotation);
			}
			$var = $this->reflectionService->getPropertyTagValues($className, $property, 'var');
			$typeAnnotationClass = $this->findAnnotationByName('Type');
			$typeAnnotation = new $typeAnnotationClass(array('value' => current($var)
			));
			$this->addAnnotation($propertyAnnotations, $typeAnnotation);
			$annotations['Properties'][$property] = $propertyAnnotations;
		}

		return $annotations;
	}

}

?>