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
 * Abstract base class for the ConfigurationProviders
 */
abstract class AbstractAnnotationProvider implements AnnotationProviderInterface {

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * @param array $annotations
	 * @param object $annotation
	 * @return void
	 */
	public function addAnnotation(&$annotations, $annotation) {
		if (is_array($annotation)) {
			foreach ($annotation as $annotation1) {
				$this->addAnnotation($annotations, $annotation1);
			}
		} else {
			$annotationClass = get_class($annotation);
			if ($annotation instanceof \TYPO3\Expose\Annotations\SingleAnnotationInterface) {
				$annotations[$annotationClass] = $annotation;
			} else {
				if (!isset($annotations[$annotationClass])) {
					$annotations[$annotationClass] = array();
				}
				$annotations[$annotationClass][] = $annotation;
			}
		}
	}

	/**
	 * @param string $annotationName
	 * @return string
	 * @throws \TYPO3\FLOW3\Error\Exception
	 */
	public function findAnnotationByName($annotationName) {
		if (class_exists($annotationName)) {
			return $annotationName;
		}
		if (class_exists('TYPO3\Expose\Annotations\\' . $annotationName)) {
			return 'TYPO3\Expose\Annotations\\' . $annotationName;
		}
		throw new \TYPO3\FLOW3\Error\Exception('No AnnotationClass for the Annotation "' . $annotationName . '" could be found', 1342706668);
	}

	/**
	 * @param mixed $input
	 * @return array
	 */
	public function convert($input) {
		if (is_array($input)) {
			return $input;
		} else {
			return array('value' => $input);
		}
	}
}

?>