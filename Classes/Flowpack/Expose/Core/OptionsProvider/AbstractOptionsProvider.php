<?php
namespace Flowpack\Expose\Core\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Reflection\PropertySchema;

/**
 */
abstract class AbstractOptionsProvider implements OptionsProviderInterface {

	/**
	 * @var PropertySchema
	 */
	protected $propertySchema;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 */
	public function __construct($propertySchema, $settings = array()) {
		$this->propertySchema = $propertySchema;
		$this->settings = $settings;
	}

}