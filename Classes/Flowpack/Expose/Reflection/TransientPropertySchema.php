<?php
namespace Flowpack\Expose\Reflection;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Reflection\ClassSchema;
use Flowpack\Expose\Utility\StringFormatter;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 */
class TransientPropertySchema extends PropertySchema {

	/**
	 *
	 * @param string $propertyName
	 * @param array $schema
	 * @param array $classSchema
	 * @param string $prefix
	 * @return void
	 */
	public function __construct($propertyName, $schema, $classSchema = NULL, $prefix = NULL) {
		$schema['name'] = $propertyName;

		if (false === isset($schema['label'])) {
			$schema['label'] = $propertyName;
		}

		parent::__construct($schema, $classSchema, $prefix);
	}

}
