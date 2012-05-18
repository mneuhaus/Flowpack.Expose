<?php
namespace Foo\ContentManagement\Reflection\Wrapper;

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
abstract class AbstractAnnotationWrapper {
	public $annotations = array();
	public $index = array();

	public function __construct($annotations) {
		$this->annotations = $annotations;
		foreach ($this->annotations as $key => $value) {
			$parts = explode("\\", $key);
			$shortName = array_pop($parts);
			$this->index[strtolower($shortName)] = $key;
		}
	}

	public function has($annotation) {
		return isset($this->annotations[$annotation]) || isset($this->index[strtolower($annotation)]);
	}

	public function set($annotation, $value) {
		$this->annotations[$annotation] = $value;
	}

	public function get($annotation) {
		if(isset($this->annotations[$annotation]))
			return $this->annotations[$annotation];

		if(isset($this->index[strtolower($annotation)]))
			return $this->annotations[$this->index[strtolower($annotation)]];
	}

	public function __call($methodName, array $arguments) {
		if (substr($methodName, 0, 3) === 'get') {
			$annotation = substr($methodName, 3);
			return $this->get($annotation);
		}
		if (substr($methodName, 0, 3) === 'has') {
			$annotation = substr($methodName, 3);
			return $this->has($annotation);
		}
		if (substr($methodName, 0, 3) === 'set') {
			$annotation = substr($methodName, 3);
			return $this->set($annotation, $arguments[0]);
		}
	}
}

?>