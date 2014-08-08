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

use Flowpack\Expose\Utility\Inflector;
use TYPO3\Flow\Annotations as Flow;

/**
 */
class DefaultSource extends AbstractSource {

	/**
	 * @var Inflector
	 * @Flow\Inject
	 */
	protected $inflector;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	public function compileSchema() {
		$schema = array(
			'listProperties' => array('__toString'),
			'searchProperties' => array(),
			'filterProperties' => array(),
			'defaultSortBy' => NULL,
			'defaultOrder' => NULL,
			'listProcessors' => array(
				'\Flowpack\Expose\Processors\SearchProcessor' => TRUE,
				'\Flowpack\Expose\Processors\FilterProcessor' => TRUE,
				'\Flowpack\Expose\Processors\PaginationProcessor' => TRUE,
				'\Flowpack\Expose\Processors\SortProcessor' => TRUE
			)
		);
		$propertyNames = $this->reflectionService->getClassPropertyNames($this->className);
		foreach ($propertyNames as $key => $propertyName) {
			$schema['properties'][$propertyName] = array(
				'name' => $propertyName,
				'label' => $this->inflector->humanizeCamelCase($propertyName, FALSE),
				'parentClassName' => $this->className,
				'position' => ( $key + 1 ) * 100,
				'infotext' => ''
			);
		}
		return $schema;
	}

}