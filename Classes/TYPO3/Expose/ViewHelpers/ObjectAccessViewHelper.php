<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow package "TYPO3.Expose".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class ObjectAccessViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $property
	 * @param string $propertyPath
	 * @param object $object
	 * @param string $className
	 * @param string $as
	 * @param string $context
	 * @return string Rendered string
	 * @api
	 */
	public function render($property = NULL, $propertyPath = NULL, $object = NULL, $as = 'value') {
		if ($property !== NULL && \TYPO3\Flow\Reflection\ObjectAccess::isPropertyGettable($object, $property)){
			$value = \TYPO3\Flow\Reflection\ObjectAccess::getProperty($object, $property);
		}
		if ($propertyPath !== NULL){
			$value = \TYPO3\Flow\Reflection\ObjectAccess::getPropertyPath($object, $property);
		}

		if (isset($value)) {
			$this->templateVariableContainer->add($as, $value);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove($as);
			return $content;
		}
	}
}

?>