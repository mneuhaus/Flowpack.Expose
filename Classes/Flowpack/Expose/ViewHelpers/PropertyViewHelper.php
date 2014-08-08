<?php
namespace Flowpack\Expose\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;

class PropertyViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 *
	 * @param object $object
	 * @param string $name
	 * @return string
	 */
	public function render($object, $name) {
		if (method_exists($object, $name)) {
			return $object->$name();
		}
		return ObjectAccess::getPropertyPath($object, $name);
	}
}