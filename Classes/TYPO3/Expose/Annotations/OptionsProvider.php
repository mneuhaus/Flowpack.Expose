<?php
namespace TYPO3\Expose\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
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
final class OptionsProvider {

	/**
	 * @var string
	 */
	public $className = '';

	/**
	 * @param string $value
	 */
	public function __construct(array $values) {
		if (isset($values['value'])) {
			$this->className = $values['value'];
		}
	}

}

?>