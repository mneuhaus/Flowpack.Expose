<?php
namespace TYPO3\Expose\Reflection\Wrapper;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 */
class PropertyAnnotationWrapper extends AbstractAnnotationWrapper {

	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @return string
	 */
	public function getElement() {
		$raw = strval($this->getType());
		$element = NULL;
		$default = 'TYPO3.Form:SingleLineText';
		$mappings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Mapping.Elements');
		if (!empty($mappings)) {
			if ($this->has('Element')) {
				$element = strval($this->get('Element'));
			}
			if ($element === NULL && isset($mappings[$raw])) {
				$element = $mappings[$raw];
			}
			if ($element === NULL && isset($mappings[strtolower($raw)])) {
				$element = $mappings[$raw];
			}
			if ($element === NULL && isset($mappings[ucfirst($raw)])) {
				$element = $mappings[$raw];
			}
			if ($element === NULL) {
				foreach ($mappings as $pattern => $element) {
					if (preg_match(('/' . $pattern) . '/', $raw) > 0) {
						break;
					}
				}
			}
		}
		if ($element === NULL && $default !== NULL) {
			$element = $default;
		}
		if ($element === NULL) {
			$element = $raw;
		}

		return $element;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		if ($this->has('Label')) {
			return strval($this->get('Label'));
		}

		return ucfirst($this->get('property'));
	}

	/**
	 * @param string $property
	 * @return void
	 */
	public function setProperty($property) {
		$this->set('property', $property);
	}

	/**
	 * @return boolean
	 */
	public function isRelationProperty() {
		#return $this->containsKey("manyToMany") ||
		return $this->containsKey('manyToOne');
	}

	/**
	 * @return string
	 */
	public function getType() {
		preg_match('/<(.+)>/', $this->get('type'), $matches);
		if (!empty($matches)) {
			return ltrim($matches[1], '\\');
		} else {
			return strval($this->get('type'));
		}
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->get('Value');
	}
}

?>