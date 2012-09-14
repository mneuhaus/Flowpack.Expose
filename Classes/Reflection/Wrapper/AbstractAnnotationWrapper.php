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

/**
 */
abstract class AbstractAnnotationWrapper {

	/**
	 * @var array
	 */
	public $annotations = array();

	/**
	 * @var array
	 */
	protected $index = array();

	/**
	 * @param array $annotations
	 */
	public function __construct($annotations) {
		$this->annotations = $annotations;
		foreach ($this->annotations as $key => $value) {
			$parts = explode('\\', $key);
			$shortName = array_pop($parts);
			$this->index[strtolower($shortName)] = $key;
		}
	}

	/**
	* @param string $methodName
	* @param array $arguments
	* @return mixed
	*/
	public function __call($methodName, array $arguments) {
		$annotation = substr($methodName, 3);
		$method = substr($methodName, 0, 3);
		switch ($method) {
			case 'get':
				return $this->get($annotation);
			case 'has':
				return $this->has($annotation);
			case 'set':
				$this->set($annotation, $arguments[0]);
				return;
		}
		trigger_error('Call to undefined method ' . get_class($this) . '::' . $methodName, E_USER_ERROR);
	}

	/**
	 * @return array
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * @param string $annotation
	 * @return mixed
	 */
	public function get($annotation) {
		if (isset($this->annotations[$annotation])) {
			return $this->annotations[$annotation];
		}
		if (isset($this->index[strtolower($annotation)])) {
			return $this->annotations[$this->index[strtolower($annotation)]];
		}
	}

	/**
	 * @param string $annotation
	 * @return boolean
	 */
	public function has($annotation) {
		return isset($this->annotations[$annotation]) || isset($this->index[strtolower($annotation)]);
	}

	/**
	 * @param string $annotation
	 * @param mixed $value
	 */
	public function set($annotation, $value) {
		$this->annotations[$annotation] = $value;
	}
}

?>