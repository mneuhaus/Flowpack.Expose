<?php
namespace TYPO3\Expose\ViewHelpers;

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
 */
class FormatAsStringViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @return string Rendered string
	 */
	public function render() {
		$value = $this->renderChildren();
		// return $this->toString($value);

			// TODO: should be retrieved differently
		$fluidTemplateTsObject = $this->templateVariableContainer->get('fluidTemplateTsObject');
		$path = $fluidTemplateTsObject->getPath() . '/stringFormatter<TYPO3.Expose:StringFormatter>';
		$fluidTemplateTsObject->getTsRuntime()->pushContext('value', $value);
		$output = $fluidTemplateTsObject->getTsRuntime()->render($path);
		$fluidTemplateTsObject->getTsRuntime()->popContext();

		return $output;
	}

	public function toString($source) {
		switch (TRUE) {
			case is_string($source):
			case is_float($source):
			case is_integer($source):
			case is_bool($source):
			case is_object($source) && method_exists($source, '__toString'):
				return strval($source);
				break;

			default:
				return '';
				break;
		}
	}
}

?>