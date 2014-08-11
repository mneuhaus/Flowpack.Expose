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
 * This OptionsProvider is very useful to add options to a select for a simple string property
 *
 * .. code-block:: yaml
 *
 *   '\TYPO3\Party\Domain\Model\ElectronicAddress':
 *       properties:
 *          type:
 *              control: 'SingleSelect'
 *              optionsProvider:
 *                  Name: Array
 *                  Options:
 *                      new: 'New'
 *                      done: 'Done'
 *                      rejected: 'Rejected'
 *
 */
class ArrayOptionsProvider extends AbstractOptionsProvider {

	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedSettings = array(
		'Options' => array(
			'default' => array(),
			'description' => 'Contains the options that will be provided',
			'required' => TRUE
		)
	);

	/**
	 * This functions returns the Options defined by a internal property
	 * or Annotations
	 *
	 * @return array $options
	 */
	public function getOptions() {
		return $this->settings['Options'];
	}

}

?>