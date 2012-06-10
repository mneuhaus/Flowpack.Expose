<?php

namespace Foo\ContentManagement\Reflection\Provider;

/* *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * abstract base class for the ConfigurationProviders
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAnnotationProvider implements AnnotationProviderInterface {
    /**
	 * @var TYPO3\FLOW3\Cache\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	public function addAnnotation(&$annotations, $annotation) {
		if(is_array($annotation)){
			foreach ($annotation as $annotation1) {
				$this->addAnnotation($annotations, $annotation1);
			}
			return;
		}
		
		$annotationClass = get_class($annotation);

		if($annotation instanceof \Foo\ContentManagement\Annotations\SingleAnnotationInterface){
			$annotations[$annotationClass] = $annotation;
		}else{
			if(!isset($annotations[$annotationClass]))
				$annotations[$annotationClass] = array();
			$annotations[$annotationClass][] = $annotation;
		}
	}

	public function findAnnotationByName($annotationName) {
		if(class_exists($annotationName))
			return $annotationName;

		if(class_exists("Foo\ContentManagement\Annotations\\" . $annotationName))
			return "Foo\ContentManagement\Annotations\\" . $annotationName;

	}
}
?>