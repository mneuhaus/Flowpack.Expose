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
 * Converter which transforms a simple type to a float, by simply casting it.
 *
 * @api
 * @FLOW3\Scope("singleton")
 */
class FloatFormatter extends \TYPO3\FLOW3\Property\TypeConverter\AbstractTypeConverter {

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('float');

    /**
     * @var string
     */
    protected $targetType = 'string';

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
        return strval($source);
    }

}

?>