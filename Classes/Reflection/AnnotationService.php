<?php

namespace Foo\ContentManagement\Reflection;

/* *
 * This script belongs to the FLOW3 framework.                            *
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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Configurationprovider for the DummyAdapter
 *
 * @version $Id: YamlConfigurationProvider.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class AnnotationService {
	/**
	 * @var TYPO3\FLOW3\Cache\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\FLOW3\Package\PackageManagerInterface
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $packageManager;

	/**
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 * @api
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $reflectionService;

	protected $runtimeCache = array();

	public function getClassAnnotations($class){
		#$implementations = class_implements("\\" . ltrim($class, "\\"));
		#if(in_array("Doctrine\ORM\Proxy\Proxy", $implementations))
		#	$class = get_parent_class("\\" . ltrim($class, "\\"));
		#$this->class = $class;

		if(!isset($this->runtimeCache[$class])){
			$annotations = array();
			$annotationProviders = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, "Foo.ContentManagement.AnnotationProvider");
			foreach($annotationProviders as $annotationProviderClass){
				$annotationProvider = new $annotationProviderClass();
				$annotations = $this->merge($annotations, $annotationProvider->getClassAnnotations($class));
			}
			$this->runtimeCache[$class] = new Wrapper\ClassAnnotationWrapper($annotations);
		}

		return $this->runtimeCache[$class];
	}

	/**
	 * returns classes that are taged with all of the specified tags
	 *
	 * @param string $tags
	 * @return void
	 * @author Marc Neuhaus
	 */
	public function getClassesAnnotatedWith($tags){
		$cache = $this->cacheManager->getCache('Admin_ImplementationCache');
		$identifier = "ClassesTaggedWith-".implode("_", $tags);

		if(!$cache->has($identifier) || true){
			$classes = array();

			$activePackages = $this->packageManager->getActivePackages();
			foreach($activePackages as $packageName => $package) {
				if(substr($packageName, 0, 8) === "Doctrine") continue;
				foreach($package->getClassFiles() as $class => $file) {
					$annotations = $this->getClassAnnotations($class);

					$tagged = true;
					foreach($tags as $tag){
						if(!$annotations->has($tag)) $tagged = false;
					}

					if($tagged)
						$classes[$class] = $packageName;
				}
			}

			$cache->set($identifier,$classes);
		}elseif(isset($this->runtimeCache[$identifier])){
			$classes = $this->runtimeCache[$identifier];
		}else{
			$this->runtimeCache[$identifier] = $classes = $cache->get($identifier);
		}
		return $classes;
	}

	public function merge($target, $source) {
		foreach ($source as $key => $value) {
			if($key == "Properties"){
				if(!isset($target[$key]))
					$target[$key] = array();
				$target[$key] = $this->merge($target[$key], $value);
			}else{
				if(is_object($value)){
					$target[$key] = $value;
				}

				if(is_array($value)){
					if(!isset($target[$key]))
						$target[$key] = array();
					$target[$key] = array_merge($target[$key], $value);
				}
			}
		}
		return $target;
	}
}
?>