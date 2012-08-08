<?php
namespace TYPO3\Admin\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              *
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
     * @var \TYPO3\Admin\Reflection\Wrapper\ClassAnnotationWrapper
     **/
    protected $annotationContext;

    /**
    * TODO: Document this Method! ( setAnnotationContext )
    */
    public function setAnnotationContext(\TYPO3\Admin\Reflection\Wrapper\ClassAnnotationWrapper $annotationWrapper) {
        $this->annotationContext = $annotationWrapper;
    }

}

?>