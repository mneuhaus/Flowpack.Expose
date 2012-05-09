<?php
namespace Foo\ContentManagement\Annotations\Wrapper;

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
 */
class ClassAnnotationWrapper extends AbstractAnnotationWrapper {
	public function getPropertyAnnotations($propertyName) {
		$properties = $this->get("properties");
		$property = new \Foo\ContentManagement\Annotations\Wrapper\PropertyAnnotationWrapper($properties[$propertyName]);
		$property->setProperty($propertyName);
		return $property;
	}

	public function getProperties() {
		$properties = array();
		foreach ($this->get("properties") as $key => $value) {
			$properties[$key] = $this->getPropertyAnnotations($key);
		}
		return $properties;
	}
}

?>