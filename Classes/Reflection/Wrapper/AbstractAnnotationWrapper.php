<?php
namespace TYPO3\Expose\Reflection\Wrapper;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
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
    * TODO: Document this Property!
    */
    public $annotations = array();

    /**
    * TODO: Document this Property!
    */
    protected $index = array();

    /**
    * TODO: Document this Method! ( __call )
    */
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

    /**
    * TODO: Document this Method! ( __construct )
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
    * TODO: Document this Method! ( getIndex )
    */
    public function getIndex() {
        return $this->index;
    }

    /**
    * TODO: Document this Method! ( get )
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
    * TODO: Document this Method! ( has )
    */
    public function has($annotation) {
        return isset($this->annotations[$annotation]) || isset($this->index[strtolower($annotation)]);
    }

    /**
    * TODO: Document this Method! ( set )
    */
    public function set($annotation, $value) {
        $this->annotations[$annotation] = $value;
    }

}

?>