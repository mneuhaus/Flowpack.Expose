<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use Flowpack\Expose\OptionsProvider\ArrayOptionsProvider;
use Flowpack\Expose\Reflection\PropertySchema;

/**
 * You can use this viewHelper to retrieve a property´s value from ab object based on the name or the schema of the property.
 * Properties with an Array OptionProvider defined, will be mapped to the option label with the current value used as index
 * Properties with a Relation OptionProvider defined, will show up with the value of the property defined on the LabelPath
 * (the last feature is only supported when you pass the propertyschema instead of the properties name)
 * 
 * Example
 * =======
 *
 * .. code-block:: html
 *
 *   <f:for each="{properties}" as="property">
 *     <e:property object="{object}" property="{property}" />
 *   </f:for>
 *
 */
class PropertyViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;
	
	/**
	 *
	 * @param object $object Object to get the property or propertyPath from
	 * @param string $name Name of the property or propertyPath
	 * @param PropertySchema $property The property´s schema
	 * @return string
	 */
	public function render($object, $name = null, PropertySchema $property = null) {
		if (null === $name && $property instanceof PropertySchema) {
			$name = $property->getPath();
		}
		if (method_exists($object, $name)) {
			$value = $object->$name();
		} else {
			$value = ObjectAccess::getPropertyPath($object, $name);
		}

		if ($property instanceof PropertySchema) {
			$schema = $property->getSchema();
			if (true === is_object($value) && true === isset($schema['optionsProvider']['LabelPath'])) {
				$value = ObjectAccess::getPropertyPath($value, $schema['optionsProvider']['LabelPath']);
			}
		}

		/* @var \Flowpack\Expose\Core\OptionsProvider\OptionsProviderInterface $optionsProvider */
		$optionsProvider = $property->getOptionsProvider();
		if ($optionsProvider instanceof ArrayOptionsProvider) {
			$options = $optionsProvider->getOptions();
			if (true === isset($options[$value])) {
				return $options[$value];
			}
		}

		return $value;
	}
	
}
