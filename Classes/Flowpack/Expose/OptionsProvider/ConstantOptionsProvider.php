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
 * .. code-block:: yaml
 *
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
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedSettings = array(
		'Regex' => array(
			'description' => 'Contains a Regular Expression to filter the class constants',
			'required' => TRUE
		),
		'EmptyOption' => array(
			'default' => NULL,
			'description' => 'Set this setting to add an emtpy option to the beginning of the options',
			'required' => FALSE
		)
	);

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
		if ($this->settings['EmptyOption'] !== NULL) {
			$constants[] = $this->settings['EmptyOption'];
		}
		foreach ($reflection->getConstants() as $key => $value) {
			if (preg_match(('/' . $regex) . '/', $key)) {
				$constants[constant(($className . '::') . $key)] = $value;
			}
		}
		return $constants;
	}

}

?>