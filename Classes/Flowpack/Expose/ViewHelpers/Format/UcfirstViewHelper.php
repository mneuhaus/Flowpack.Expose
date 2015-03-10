<?php
namespace Flowpack\Expose\ViewHelpers\Format;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * returns the string with the first character capitalized
 *
 * given a variable named ``foo`` contains a string ``fooBar``
 *
 * .. code-block:: html
 *
 *   {foo -> e:format.ucfirst()}
 *
 * Will result in:
 *
 * .. code-block:: htlm
 *
 *   FooBar
 */
class UcfirstViewHelper extends AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * Uppercase first character
	 *
	 * @return string The altered string.
	 */
	public function render() {
		return ucfirst($this->renderChildren());
	}
}
