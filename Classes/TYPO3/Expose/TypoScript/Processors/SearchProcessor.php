<?php
namespace TYPO3\Expose\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 */
class SearchProcessor extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$object = $this->tsValue('objects')->getFirst();
		if (!is_object($object)) {
			return $this->tsValue('objects');
		}

		$schema = $this->getSchema($this->tsValue('objects')->getFirst());

		if (!isset($schema['searchPaths'])) {
			return $this->tsValue('objects');
		}

		$search = $this->getSearch();
		if ($search !== NULL) {
			$query = $this->tsValue('objects')->getQuery();
			$constraints = array();
			foreach ($schema['searchPaths'] as $searchPath) {
				$constraints[] = $query->like($searchPath, '%' . $search . '%', FALSE);
			}
			$constraint = $query->logicalAnd(
				$query->getConstraint(),
				$query->logicalOr($constraints)
			);
			$query->matching($constraint);

			return $query->execute();
		}

		return $this->tsValue('objects');
	}

	/**
	 * @return string
	 */
	public function getSearch() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		if ($request->hasArgument('search')) {
			return $request->getArgument('search');
		}
		return NULL;
	}

	public function getSchema($object) {
		$className = $this->getClassName($object);
		$this->tsRuntime->pushContext('object', $object);
		$this->tsRuntime->pushContext('className', $className);
		$schema = $this->tsRuntime->render('schemaLoader<TYPO3.Expose:SchemaLoader>');
		$this->tsRuntime->popContext();
		$this->tsRuntime->popContext();
		return $schema;
	}

	public function getClassName($object) {
		return ltrim($this->reflectionService->getClassNameByObject($object), '\\');
	}
}
?>