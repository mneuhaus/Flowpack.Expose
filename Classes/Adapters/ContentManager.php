<?php

namespace Foo\ContentManagement\Adapters;

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
 * ContentManager to retrieve and Initialize Adapters
 *
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class ContentManager {
	/**
	 * @var \Foo\ContentManagement\Core\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \Foo\ContentManagement\Core\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * Containes initialized Adapters
	 * @var array
	 */
	var $adapters = array();

	/**
	 * Currently active Adapter
	 * 
	 * @var object
	 **/
	var $currentAdapter = null;

	public function __construct(\Foo\ContentManagement\Core\ConfigurationManager $configurationManager, \TYPO3\FLOW3\Object\ObjectManagerInterface $objectManager) {
		$this->configurationManager = $configurationManager;
		$this->objectManager = $objectManager;
		$this->adapters = $this->getAdapters();
	}

	/**
	 * return the Adapter responsible for the class
	 *
	 * @return $groups Array
	 * @author Marc Neuhaus
	 */
	public function getAdapterByClass($class){
		$implementations = class_implements("\\" . ltrim($class, "\\"));
		if(in_array("Doctrine\ORM\Proxy\Proxy", $implementations))
			$class = get_parent_class("\\" . ltrim($class, "\\"));

		$this->adapters = $this->getAdapters();
		
		$cache = $this->cacheManager->getCache('Admin_Cache');
		$identifier = "AdaptersByBeing-".sha1($class);
		
		if(!$cache->has($identifier) || true){
			$adaptersByBeings = array();
			foreach ($this->adapters as $adapter) {
				foreach ($adapter->getGroups() as $group => $being) {
					foreach ($being as $conf) {
						$adaptersByBeings[$conf["being"]] = get_class($adapter);
					}
				}
			}
			
			$cache->set($identifier,$adaptersByBeings);
		}else{
			$adaptersByBeings = $cache->get($identifier);
		}

		#$class = \Foo\ContentManagement\Core\API::get("classShortNames", $class);
		#var_dump($adaptersByBeings, $class);
		$adapterClass = $adaptersByBeings[$class];

		return $this->adapters[$adapterClass];
	}

	public function getAdapter() {
		return $this->currentAdapter;
	}

	public function setAdapterByClass($class) {
		$this->currentAdapter = $this->getAdapterByClass($class);
	}
	
	/**
	 * Returns all active adapters
	 *
	 * @return $adapters
	 * @author Marc Neuhaus
	 */
	public function getAdapters(){
		$settings = $this->configurationManager->getSettings();
		$adapters = array();
		foreach ($settings["Adapters"] as $adapter => $active) {
			if($active == "active"){
				$adapters[$adapter] = $this->objectManager->get($adapter);
			}
		}
		return $adapters;
	}

	public function getClassAnnotations($class) {
		$classConfiguration = $this->configurationManager->getClassConfiguration($class);
		return $classConfiguration;
	}

	/**
	 * returns all active groups
	 *
	 * @return $groups Array
	 * @author Marc Neuhaus
	 */
	public function getGroups(){
		$cache = $this->cacheManager->getCache('Admin_Cache');
		$identifier = "Groups-".sha1(implode("-", array_keys($this->adapters)));
		
		if(!$cache->has($identifier)){
			$groups = array();
			$adapters = array();
			foreach ($this->adapters as $adapter) {
				foreach ($adapter->getGroups() as $group => $beings) {
					foreach ($beings as $conf) {
						$being = $conf["being"];
						$conf["adapter"] = get_class($adapter);
						$groups[$group]["beings"][$being] = $conf;
					}
				}
			}

			$cache->set($identifier,$groups);
		}else{
			$groups = $cache->get($identifier);
		}

		return $groups;
	}

	/**
	 * get the group which the class belongs to
	 *
	 * @param string $class 
	 * @return $group string
	 * @author Marc Neuhaus
	 */
	public function getGroupByClass($class){
		foreach ($this->adapters as $adapter) {
			foreach ($adapter->getGroups() as $group => $beings) {
				foreach ($beings as $beingName => $conf) {
					if($class == $beingName)
						return $group;
				}
			}
		}
	}

	public function getId($object) {
        return $this->getAdapterByClass(get_class($object))->getId($object);
    }

	public function getObjects($class) {
		$class = ltrim($class, "\\");
		return $this->getAdapterByClass($class)->getObjects($class);
	}

	public function getPropertyAnnotations($class, $property) {
		$classConfiguration = $this->configurationManager->getClassConfiguration($class);
		return $classConfiguration["properties"][$property];
	}

	public function getProperties($object) {
		$properties = $this->getClassAnnotations(get_class($object))->getProperties();
		foreach ($properties as $property => $value) {
			$properties[$property]["value"] = \TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($object, $property);
		}
		return $properties;
	}

	public function getString($object) {
		return sprintf("%s:%s", get_class($object), $this->getId($object));
	}

	public function isNewObject($object) {
		return $this->getAdapterByClass(get_class($object))->isNewObject($object);
	}

	public function toString($value) {
		
	}
}
?>