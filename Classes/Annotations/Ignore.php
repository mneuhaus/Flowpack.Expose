<?php
namespace TYPO3\Expose\Annotations;

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
 * @Annotation
 */
final class Ignore implements SingleAnnotationInterface {

	/**
	 * @var string
	 */
	public $views = '';

	/**
	 * @param array $values
	 */
	public function __construct(array $values) {
		if (isset($values['value']) && $values['value'] !== TRUE) {
			$this->views = $values['value'];
		}
	}

	/**
	 * @param string $context
	 * @return boolean
	 */
	public function ignoreContext($context) {
		return empty($this->views) || stristr($this->views, $context);
	}
}

?>