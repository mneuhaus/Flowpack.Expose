<?php
namespace Foo\ContentManagement\Annotations;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * @Annotation
 */
abstract class ContextAwareAnnotation {
	/**
	 *
	 * @var \Foo\ContentManagement\Reflection\Wrapper\ClassAnnotationWrapper
	 **/
	protected $annotationContext;

	public function setAnnotationContext(\Foo\ContentManagement\Reflection\Wrapper\ClassAnnotationWrapper $annotationWrapper) {
		$this->annotationContext = $annotationWrapper;
	}
}

?>