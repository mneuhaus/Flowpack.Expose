<?php
namespace TYPO3\Expose\Core;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @FLOW3\Scope("singleton")
 */
class CacheManager {

	/**
	 * @var \TYPO3\FLOW3\Cache\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var array
	 */
	protected $cacheIdentifiers = array(
		'Expose_Cache',
		'Expose_TemplateCache',
		'Expose_ActionCache',
		'Expose_ImplementationCache',
		'TYPO3_Expose_ShortNames',
		'TYPO3_Expose_Annotations'
	);

	/**
	 * @param \TYPO3\FLOW3\Cache\CacheManager $cacheManager
	 */
	public function injectCacheManager(\TYPO3\FLOW3\Cache\CacheManager $cacheManager) {
		$this->cacheManager = $cacheManager;
	}

	/**
	 * @param string $cacheIdentifier
	 * @return \TYPO3\FLOW3\Cache\Frontend\FrontendInterface
	 */
	public function getCache($cacheIdentifier) {
		return $this->cacheManager->getCache($cacheIdentifier);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function createIdentifier($string) {
		return preg_replace('/[\\/\\/:\\.\\\\\\?%=]+/', '_', $string);
	}

	/**
	 * @return void
	 */
	public function flushExposeCaches() {
		foreach ($this->cacheIdentifiers as $cacheIdentifier) {
			$this->getCache($cacheIdentifier)->flush();
		}
	}

	/**
	 * @param string $fileMonitorIdentifier
	 * @param array $changedFiles
	 * @return void
	 */
	public function flushCachesByChangedFiles($fileMonitorIdentifier, array $changedFiles) {
		$this->flushExposeCaches();
	}
}

?>