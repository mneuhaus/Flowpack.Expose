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
	public function getPropertyAnnotations($property) {
		$properties = $this->get("properties");
		return new \Foo\ContentManagement\Annotations\Wrapper\PropertyAnnotationWrapper($properties[$property]);
	}
}

?>