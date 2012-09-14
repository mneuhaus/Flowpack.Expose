<?php
namespace TYPO3\Expose\Core;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 * @FLOW3\Scope("singleton")
 */
class CacheManager {

    /**
     * @var TYPO3\FLOW3\Cache\CacheManager
     * @FLOW3\Inject
     */
    protected $cacheManager;

    /**
     * @var array
     */
    protected $caches = array('Expose_Cache',
    	'Expose_TemplateCache',
    	'Expose_ActionCache',
    	'Expose_ImplementationCache',
    	'TYPO3_Expose_ShortNames',
    	'TYPO3_Expose_Annotations'
    );

    /**
    * TODO: Document this Method! ( injectCacheManager )
    */
    public function injectCacheManager(\TYPO3\FLOW3\Cache\CacheManager $cacheManager) {
        $this->cacheManager = $cacheManager;
    }

    /**
    * TODO: Document this Method! ( getCache )
    */
    public function getCache($cache) {
        return $this->cacheManager->getCache($cache);
    }

    /**
    * TODO: Document this Method! ( createIdentifier )
    */
    public function createIdentifier($string) {
        return preg_replace('/[\\/\\/:\\.\\\\\\?%=]+/', '_', $string);
    }

    /**
    * TODO: Document this Method! ( flushExposeCaches )
    */
    public function flushExposeCaches() {
        foreach ($this->caches as $cache) {
            $this->getCache($cache)->flush();
        }
    }

    /**
    * TODO: Document this Method! ( flushCachesByChangedFiles )
    */
    public function flushCachesByChangedFiles($fileMonitorIdentifier, array $changedFiles) {
        $this->flushExposeCaches();
    }

}

?>