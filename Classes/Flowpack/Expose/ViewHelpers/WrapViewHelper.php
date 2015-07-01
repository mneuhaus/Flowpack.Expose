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
class WrapViewHelper extends AbstractViewHelper {
	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;
	/**
	 * Constructor
	 *
	 * @api
	 */
	public function __construct() {
		$this->registerArgument('name', 'string', 'Name of the Wrapper', TRUE);
		$this->registerArgument('arguments', 'array', 'Arguments supplied to the callback applying this wrapper', FALSE);
	}

	/**
	 *
	 * @param string $name Name of the Wrapper
	 * @param array $arguments Arguments supplied to the callback applying this wrapper
	 * @return string Rendered string
	 * @api
	 */
	public function render($name, $arguments = array()) {
		$content = $this->renderChildren();
		if ($this->viewHelperVariableContainer->exists('Flowpack\Expose\ViewHelpers\WrapViewHelper', $name)) {
			$wraps = $this->viewHelperVariableContainer->get('Flowpack\Expose\ViewHelpers\WrapViewHelper', $name);
			/* @var $wrap \Flowpack\Expose\Core\QueryBehaviors\AbstractQueryBehavior */
			foreach ($wraps as $wrap) {
				if (false === $arguments['property'] instanceof TransientPropertySchema || true === $wrap::$appliedOnTransientProperties) {
					$content = $wrap->wrap($content, $arguments);
				}
			}
		}
		return $content;
	}
}

?>