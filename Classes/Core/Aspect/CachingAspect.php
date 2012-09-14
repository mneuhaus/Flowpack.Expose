<?php
namespace TYPO3\Expose\Core\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Caching of findMatchResults() and resolve() calls on the web Router.
 *
 * @FLOW3\Aspect
 * @FLOW3\Scope("singleton")
 */
class CachingAspect {

    /**
     * @var \TYPO3\FLOW3\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     */
    protected $reflectionService;

    /**
     * Injects the CacheManager
     *
     * @param \TYPO3\FLOW3\Cache\CacheManager $cacheManager
     * @return void
     */
    public function injectCacheManager(\TYPO3\FLOW3\Cache\CacheManager $cacheManager) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Injects the CacheManager
     *
     * @param \TYPO3\FLOW3\Reflection\ReflectionService $reflectionService
     * @return void
     */
    public function injectReflectionService(\TYPO3\FLOW3\Reflection\ReflectionService $reflectionService) {
        $this->reflectionService = $reflectionService;
    }

    /**
     * Around advice
     *
     * @FLOW3\Around("methodAnnotatedWith(TYPO3\Expose\Annotations\Cache)")
     * @param \TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint The current join point
     * @return array Result of the target method
     */
    public function cacheMethodsAnnotatedWithCacheAnnotation(\TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint) {
        $cache = $this->cacheManager->getCache($this->createCacheName($joinPoint));
        $cacheIdentifier = $this->createCacheIdentifier($joinPoint);
        if ($cache->has($cacheIdentifier)) {
            return $cache->get($cacheIdentifier);
        }
        $result = $joinPoint->getAdviceChain()->proceed($joinPoint);
        $cache->set($cacheIdentifier, $result);
        return $result;
    }

    /**
    * TODO: Document this Method! ( convertValue )
    */
    public function convertValue($value) {
        switch (true) {
        case is_array($value):
            foreach ($value as $k => $v) {
                $value[$k] = $this->convertValue($v);
            }
            return implode('_', $value);
        case is_object($value):
            return spl_object_hash($value);
        case is_int($value):

        case is_float($value):

        case is_string($value):

        case is_bool($value):
            return preg_replace('/[\\/\\/:\\.\\\\\\?%=]+/', '_', strval($value));
        default:
            return '';
        }
    }

    /**
    * TODO: Document this Method! ( createCacheIdentifier )
    */
    public function createCacheIdentifier($joinPoint) {
        return md5(($this->convertValue($joinPoint->getClassName()) . $this->convertValue($joinPoint->getMethodName())) . $this->convertValue($joinPoint->getMethodArguments()));
    }

    /**
    * TODO: Document this Method! ( createCacheName )
    */
    public function createCacheName($joinPoint) {
        return sprintf('%s-%s', $this->convertValue($joinPoint->getClassName()), $this->convertValue($joinPoint->getMethodName()));
    }

}

?>