<?php
namespace Flowpack\Expose\Reflection\Sources;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\Sources\AbstractSchemaSource;
use TYPO3\Flow\Annotations as Flow;

/**
 */
class AnnotationSource extends AbstractSchemaSource {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	public function compileSchema() {
		$schema = array('properties' => array());
		$propertyNames = $this->reflectionService->getClassPropertyNames($this->className);
		foreach ($propertyNames as $key => $propertyName) {
			$propertySchema = array();
			$propertySchema['annotations'] = $this->reflectionService->getPropertyAnnotations($this->className, $propertyName);
			$schema['properties'][$propertyName] = $propertySchema;
		}
		return $schema;
	}


}

?>