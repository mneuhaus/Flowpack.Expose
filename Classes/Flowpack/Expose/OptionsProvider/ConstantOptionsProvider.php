<?php
namespace Flowpack\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\OptionsProvider\AbstractOptionsProvider;

/**
 *
 * This OptionsProvider is used to load options from an Entities class
 * by using a regular expression to match existing constants
 *
 * Example:
 *      TYPO3\Party\Domain\Model\ElectronicAddress:
 *          Properties:
 *              type:
 *                  Element: TYPO3.Form:SingleSelectDropdown
 *                  OptionsProvider:
 *                      Name: ConstOptionsProvider
 *                      Regex: TYPE_.+
 *
 */
class ConstantOptionsProvider extends AbstractOptionsProvider {
	/**
	 * Load the Options by searching the Entities constants based on the specified regular
	 * expression
	 *
	 * @return array $options
	 */
	public function getOptions() {
		$className = $this->propertySchema->getClassName();
		$reflection = new \ReflectionClass($className);
		$regex = $this->settings['Regex'];
		$constants = array();
		foreach ($reflection->getConstants() as $key => $value) {
			if (preg_match(('/' . $regex) . '/', $key)) {
				$constants[constant(($className . '::') . $key)] = $value;
			}
		}
		return $constants;
	}

}

?>