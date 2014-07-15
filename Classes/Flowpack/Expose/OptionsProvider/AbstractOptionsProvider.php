<?php
namespace Flowpack\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
abstract class AbstractOptionsProvider implements OptionsProviderInterface {

	/**
	 * @var array
	 */
	protected $propertySchema;

	/**
	 */
	public function __construct($propertySchema = array()) {
		$this->propertySchema = $propertySchema;
	}

}

?>