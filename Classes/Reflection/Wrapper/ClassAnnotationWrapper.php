<?php
namespace Foo\ContentManagement\Reflection\Wrapper;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class ClassAnnotationWrapper extends AbstractAnnotationWrapper {
	public function getPropertyAnnotations($propertyName) {
		$properties = $this->get("Properties");
		$property = new \Foo\ContentManagement\Reflection\Wrapper\PropertyAnnotationWrapper($properties[$propertyName]);
		$property->setProperty($propertyName);
		$property->setClass($this->getClass());
		if($this->has("Object")){
			if(\TYPO3\FLOW3\Reflection\ObjectAccess::isPropertyGettable($this->get("Object"), $propertyName))
				$property->setValue(\TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($this->get("Object"), $propertyName));
		}
		return $property;
	}

	public function getProperties($context = null) {
		$properties = array();
		foreach ($this->get("properties") as $property => $value) {
			$properties[$property] = $this->getPropertyAnnotations($property);

			if(!is_null($context) 
				&& $properties[$property]->has("ignore")
				&& $properties[$property]->getIgnore()->ignoreContext($context))
				unset($properties[$property]);
		}
		return $properties;
	}

	public function getSets() {
		return array("" => $this->getProperties());
	}
}

?>