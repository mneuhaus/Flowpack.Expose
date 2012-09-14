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

/**
 * Interface for the ReflectionProviders
 */
interface AnnotationProviderInterface {

	/**
	 * @param string $className
	 * @return array
	 */
	public function getClassAnnotations($class);

}

?>