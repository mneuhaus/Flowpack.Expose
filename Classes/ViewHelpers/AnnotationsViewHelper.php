<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @api
 */
class AnnotationsViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Expose\Reflection\AnnotationService
	 * @FLOW3\Inject
	 */
	protected $annotationService;

	/**
	 * @param object $object
	 * @param string $className
	 * @param string $as
	 * @return string Rendered string
	 * @api
	 */
	public function render($object = NULL, $className = NULL, $as = 'annotations') {
		if ($className === NULL && $object !== NULL) {
			$className = get_class($object);
		}
		$classAnnotations = $this->annotationService->getClassAnnotations($className);
		$this->templateVariableContainer->add($as, $classAnnotations);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		return $content;
	}
}

?>