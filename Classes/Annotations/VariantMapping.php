<?php
namespace TYPO3\Expose\Annotations;

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
 * @Annotation
 */
final class VariantMapping {

    /**
     * @var array
     */
    public $mappings = array();

    /**
     * @var object
     */
    protected $object = null;

    /**
    * TODO: Document this Method! ( __call )
    */
    public function __call($method, $arguments) {
        if (substr($method, 0, 3) == 'get') {
            return $this->getMapping(lcfirst(substr($method, 3)));
        }
        return false;
    }

    /**
     * @param string $value
     */
    public function __construct(array $values = array()) {
        $this->mappings = $values;
    }

    /**
    * TODO: Document this Method! ( getMapping )
    */
    public function getMapping($key) {
        if (isset($this->mappings[$key])) {
            $property = $this->mappings[$key];
            if (isset($this->object->__properties[$property])) {
                return $this->object->__properties[$property]->value;
            }
            return \TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($this->object->object, $property);
        }
        if (isset($this->object->__properties[$key])) {
            return $this->object->__properties[$key]->value;
        }
        #		$getter = "get" . ucfirst($key);
        #		if(method_exists($this->object->object, $getter))
        #			return call_user_func(array($this->object->object, $getter));
        return false;
    }

    /**
    * TODO: Document this Method! ( hasMapping )
    */
    public function hasMapping($key) {
        return isset($this->mappings[$key]);
    }

    /**
    * TODO: Document this Method! ( setObject )
    */
    public function setObject($object) {
        $this->object = $object;
    }

}

?>