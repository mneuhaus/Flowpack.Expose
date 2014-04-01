<?php
namespace TYPO3\Expose\I18n;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\I18n\Cldr\CldrModel;
use TYPO3\Flow\I18n\Configuration;
use TYPO3\Flow\I18n\Locale;
use TYPO3\Flow\Utility\Files;

/**
 * A Service which provides further information about a given locale
 * and the current state of the i18n and L10n components.
 *
 * @Flow\Scope("singleton")
 * @api
 */
class Service extends \TYPO3\Flow\I18n\Service {

	/**
	 * @Flow\Inject(lazy=false)
	 * @var \TYPO3\Flow\I18n\Cldr\CldrRepository
	 */
	protected $cldrRepository;

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * Initializes the locale service
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration('Settings', 'TYPO3.Flow.i18n');
		var_dump($this->settings);

		$this->configuration = new Configuration($this->settings['defaultLocale']);
		$this->configuration->setFallbackRule($this->settings['fallbackRule']);

		if ($this->cache->has('availableLocales')) {
			$this->localeCollection = $this->cache->get('availableLocales');
		} else {
			$this->generateAvailableLocalesCollectionByScanningFilesystem();
			$this->cache->set('availableLocales', $this->localeCollection);
		}
	}

	public function injectSettings(array $settings) {
	}

	/**
	 * @param \TYPO3\Flow\I18n\Locale $locale Desired locale of localized file
	 * @return string
	 * @see Configuration::setFallbackRule()
	 * @api
	 */
	public function getCurrencies(Locale $locale = NULL) {
		return $this->getModelData('numbers/currencies', 'type', $locale);
	}

	/**
	 * @param string $currencyCode iso code of the requested currency
	 * @param \TYPO3\Flow\I18n\Locale $locale Desired locale of localized file
	 * @return string
	 * @see Configuration::setFallbackRule()
	 * @api
	 */
	public function getCurrency($currencyCode, Locale $locale = NULL) {
		$currencies = $this->getModelData('numbers/currencies', 'type', $locale);
		return $currencies[$currencyCode];
	}

	/**
	 * @param \TYPO3\Flow\I18n\Locale $locale Desired locale of localized file
	 * @return string
	 * @see Configuration::setFallbackRule()
	 * @api
	 */
	public function getLanguages(Locale $locale = NULL) {
		return $this->getModelData('localeDisplayNames/languages', 'type', $locale);
	}

	/**
	 * @param \TYPO3\Flow\I18n\Locale $locale Desired locale of localized file
	 * @return string
	 * @see Configuration::setFallbackRule()
	 * @api
	 */
	public function getTerritory(Locale $locale = NULL) {
		return $this->getModelData('localeDisplayNames/territories', 'type', $locale);
	}

	/**
	 * @param string $path
	 * @param string $keyAttribute
	 * @param \TYPO3\Flow\I18n\Locale $locale Desired locale of localized file
	 * @return string
	 * @see Configuration::setFallbackRule()
	 * @api
	 */
	protected function getModelData($path, $keyAttribute, Locale $locale = NULL) {
		if ($locale === NULL) {
			$locale = $this->configuration->getCurrentLocale();
		}
		$cacheIdentifier = sha1($locale->__toString() . $path . $keyAttribute);

		$data = $this->cache->get($cacheIdentifier);
		if ($data === FALSE) {
			$model = $this->cldrRepository->getModelForLocale($locale);

			$rawData = $model->getRawData($path);
			$data = array();
			foreach ($rawData as $key => $value) {
				$key = CldrModel::getAttributeValue($key, $keyAttribute);
				$data[$key] = $value;
			}
			$this->cache->set($cacheIdentifier, $data);
		}

		return $data;
	}

}
