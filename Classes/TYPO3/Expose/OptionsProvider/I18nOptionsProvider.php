<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * OptionsProvider for related Beings
 *
 */
class I18nOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

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
		switch (strtolower($this->propertySchema['optionsProvider']['type'])) {
			case 'country':
			case 'countries':
				$territories = $this->i18nService->getTerritory();
				foreach ($territories as $key => $value) {
					if (!is_numeric($key)) {
						$options[$key] = $value;
					}
				}
				break;

			default:
				# code...
				break;
		}
		return $options;
	}

}

?>