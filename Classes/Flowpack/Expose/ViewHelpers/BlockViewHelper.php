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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class BlockViewHelper extends AbstractViewHelper {
	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;
	/**
	 *
	 * @param string $name
	 * @return string Rendered string
	 * @api
	 */
	public function render($name) {
		if ($this->viewHelperVariableContainer->exists('Flowpack\Expose\ViewHelpers\BlockViewHelper', $name)) {
			$block = $this->viewHelperVariableContainer->get('Flowpack\Expose\ViewHelpers\BlockViewHelper', $name);
			return implode(chr(10), $block);
		}

	}
}

?>