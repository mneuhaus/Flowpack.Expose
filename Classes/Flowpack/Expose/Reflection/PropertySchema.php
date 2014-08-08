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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 */
class PropertySchema {
	/**
	 * @Flow\Inject
	 * @var ReflectionService
	 */
	protected $reflectionService;

	/**
	 * the class name to build the form for
	 *
	 * @var string
	 */
	protected $className;

	/**
	 * @var ClassSchema
	 */
	protected $classSchema;

	/**
	 * @var string
	 */
	protected $formName;

	/**
	 * @var array
	 */
	protected $schema;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 *
	 * @param string $schema
	 * @param strign $prefix
	 * @return void
	 */
	public function __construct($schema, $classSchema, $prefix = NULL) {
		$this->schema = $schema;
		$this->className = $classSchema->getClassName();
		$this->classSchema = $classSchema;
		$this->prefix = $prefix;
	}

	public function __toString() {
		return $this->getName();
	}

	public function getName() {
		return $this->schema['name'];
	}

	public function getPath() {
		return $this->prefix === NULL ? $this->schema['name'] : $this->prefix . '.' . $this->schema['name'];
	}

	public function getLabel() {
		return $this->schema['label'];
	}

	public function getPosition() {
		return $this->schema['@position'];
	}

	public function getInfotext() {
		return $this->schema['infotext'];
	}

	public function getType() {
		return $this->schema['type'];
	}

	public function getElementType() {
		return $this->schema['elementType'];
	}

	public function getControl() {
		return $this->schema['control'];
	}

	public function setControl($control) {
		$this->schema['control'] = $control;
	}

	public function getClassName() {
		return $this->className;
	}

	public function getClassSchema() {
		return $this->classSchema;
	}

	public function getOptionsProvider() {
		if (class_exists($this->getType())) {
			if ($this->reflectionService->isClassAnnotatedWith($this->getType(), '\TYPO3\Flow\Annotations\Entity')) {
				return new \Flowpack\Expose\OptionsProvider\RelationOptionsProvider($this);
			}
		}

		if (($this->getType() === 'array' || $this->getType() === 'SplObjectStorage' || $this->getType() === '\Doctrine\Common\Collections\Collection' || $this->getType() === '\Doctrine\Common\Collections\ArrayCollection')) {
			return new \Flowpack\Expose\OptionsProvider\RelationOptionsProvider($this);
		}

		if (isset($this->schema['optionsProvider'])) {
			$settings = $this->schema['optionsProvider'];
			$className = $this->schema['optionsProvider']['Name'];
			if (!class_exists($className)) {
				$className = '\Flowpack\Expose\OptionsProvider\\' . $className . 'OptionsProvider';
			}
			return new $className($this, $settings);
		}
	}

	/**
	 * @param string $formName
	 */
	public function setFormName($formName) {
		$this->formName = $formName;
	}

	/**
	 * @return string
	 */
	public function getFormName() {
		return $this->formName;
	}
}

?>