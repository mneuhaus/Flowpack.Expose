<?php
namespace TYPO3\Expose\TypoScript\Objects\Schema\Sources;

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
 * Render a Form section using the Form framework
 */
class TypoScriptSource extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @param string $className
	 * @return void
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	public function getClassName() {
		return $this->tsValue('className');
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$schema = array();

		$schemaNamePath = $this->path . '/schemaName';
		$schemaName = $this->tsRuntime->render($schemaNamePath);
		$className = str_replace('\\', '.', $schemaName);

		$schemaPath = $this->path . '/<TYPO3.Expose:Schema:' . $className . '>';
		if ($this->tsRuntime->canRender($schemaPath)) {
			$schema = $this->tsRuntime->render($schemaPath);
		}

		if (isset($schema['properties'])) {
			foreach ($schema['properties'] as $key => $value) {
				if (isset($value['__meta']['position'])) {
					$schema['properties'][$key]['@position'] = $value['__meta']['position'];
				}
			}
		}

		return $schema;
	}
}

?>