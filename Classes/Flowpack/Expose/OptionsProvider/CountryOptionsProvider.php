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
use TYPO3\Flow\Annotations as Flow;

/**
 * This OptionsProvider is provides a localized list of countries
 */
class CountryOptionsProvider extends AbstractOptionsProvider {
	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedSettings = array(
		'EmptyOption' => array(
			'default' => NULL,
			'description' => 'Set this setting to add an emtpy option to the beginning of the options',
			'required' => FALSE
		)
	);

	/**
	 * @var \TYPO3\Flow\I18n\Service
	 * @Flow\Inject;
	 */
	protected $i18nService;

	/**
	 * This functions returns the Options defined by a internal property
	 * or Annotations
	 *
	 * @return array $options
	 */
	public function getOptions() {
		$options = array();

		if ($this->settings['EmptyOption'] !== NULL) {
			$constants[] = $this->settings['EmptyOption'];
		}

		$territories = $this->i18nService->getTerritory();
		foreach ($territories as $key => $value) {
			if (!is_numeric($key)) {
				$options[$key] = $value;
			}
		}
		return $options;
	}

}

?>