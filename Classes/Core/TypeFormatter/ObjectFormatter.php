<?php
namespace TYPO3\Admin\Core\TypeFormatter;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 *
 * @api
 * @FLOW3\Scope("singleton")
 */
class ObjectFormatter extends \TYPO3\FLOW3\Property\TypeConverter\AbstractTypeConverter {

    /**
     * @var \TYPO3\Admin\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

    /**
     * @var integer
     */
    protected $nestingLevel = 0;

    /**
     * @var integer
     */
    protected $priority = 10;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @api
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('object'
    );

    /**
     * @var string
     */
    protected $targetType = 'string';

    /**
    * TODO: Document this Method! ( getProperties )
    */
    public function getProperties($source) {
        if ($source instanceof \Doctrine\ORM\Proxy\Proxy) {
            $class = get_parent_class($source);
        } else {
            $class = get_class($source);
        }
        $schema = $this->reflectionService->getClassSchema($class);
        if (is_object($schema)) {
            $properties = $schema->getProperties();
        } else {
            $properties = array_flip($this->reflectionService->getClassPropertyNames($class));
        }
        return $properties;
    }

    /**
    * TODO: Document this Method! ( getStringByAnnotation )
    */
    public function getStringByAnnotation($source) {
        $annotations = $this->annotationService->getClassAnnotations(get_class($source));
        $annotations->setObject($source);
        $title = array();
        foreach ($annotations->getProperties() as $property) {
            if ($property->has('title')) {
                $title[] = $property->getValue();
            }
        }
        if (count($title) > 0) {
            return implode(', ', $title);
        }
        return false;
    }

    /**
    * TODO: Document this Method! ( getStringByGuessing )
    */
    public function getStringByGuessing($source) {
        $annotations = $this->annotationService->getClassAnnotations(get_class($source));
        $goodGuess = array();
        $usualSuspects = array('title',
        	'name'
        );
        foreach ($annotations->getProperties() as $property) {
            if (in_array($property->getIndex(), $usualSuspects)) {
                if (\TYPO3\FLOW3\Reflection\ObjectAccess::isPropertyGettable($source, $property->getProperty())) {
                    $value = \TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($source, $property->getProperty());
                    if (is_object($value) && $this->nestingLevel < 3) {
                        $this->nestingLevel++;
                        $value = $this->convertFrom($value, 'string');
                        $this->nestingLevel--;
                    }
                    if (!is_object($value) && !is_null($value)) {
                        $goodGuess[] = $value;
                    }
                }
            }
        }
        if (count($goodGuess) > 0) {
            return implode(', ', $goodGuess);
        }
        return false;
    }

    /**
    * TODO: Document this Method! ( getStringByProperties )
    */
    public function getStringByProperties($source) {
        $properties = $this->getProperties($source);
        $strings = array();
        $count = 0;
        foreach ($properties as $key => $meta) {
            if ($count > 3) {
                break;
            }
            if ($key !== 'FLOW3_Persistence_Identifier') {
                if (\TYPO3\FLOW3\Reflection\ObjectAccess::isPropertyGettable($source, $key)) {
                    $value = \TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($source, $key);
                    if (is_string($value)) {
                        $strings[] = $value;
                        $count++;
                    } elseif (is_object($value) && $this->nestingLevel < 3) {
                        $this->nestingLevel++;
                        $string = $this->convertFrom($value, 'string');
                        if (!is_null($string)) {
                            $strings[] = $string;
                            $count++;
                        }
                        $this->nestingLevel--;
                    }
                }
            }
        }
        if (!empty($strings)) {
            return implode(', ', $strings);
        }
        return false;
    }

    /**
     * This implementation always returns TRUE for this method.
     *
     * @param mixed $source the source data
     * @param string $targetType the type to convert to.
     * @return boolean TRUE if this TypeConverter can convert from $source to $targetType, FALSE otherwise.
     * @api
     */
    public function canConvertFrom($source, $targetType) {
        return true;
    }

    /**
     * Actually convert from $source to $targetType, by doing a typecast.
     *
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\FLOW3\Property\PropertyMappingConfigurationInterface $configuration
     * @return float
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = array(), \TYPO3\FLOW3\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
        if (method_exists($source, '__toString')) {
            return strval($source);
        }
        if ($string = $this->getStringByAnnotation($source)) {
            return $string;
        }
        if ($string = $this->getStringByGuessing($source)) {
            return $string;
        }
        if ($string = $this->getStringByProperties($source)) {
            return $string;
        }
        if ($this->nestingLevel > 0) {
            return null;
        }
        return sprintf('Object (%s)', get_class($this));
    }

}

?>