<?php

namespace Foo\ContentManagement\Core\Adapters;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * abstract base class for the Adapters
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAdapter implements \Foo\ContentManagement\Core\Adapters\AdapterInterface {
	/**
	 * @var \Foo\ContentManagement\Reflection\AnnotationService
	 * @FLOW3\Inject
	 */
	protected $annotationService;
		
	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;
	
	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @api
	 * @FLOW3\Inject
	 */
	protected $objectManager;
	
	/**
	 * @var \TYPO3\FLOW3\Package\PackageManagerInterface
	 * @FLOW3\Inject
	 */
	protected $packageManager;
	
	/**
	 * @var TYPO3\FLOW3\Property\PropertyMapper
	 * @api
	 * @FLOW3\Inject
	 */
	protected $propertyMapper;
	
	/**
	 * @var \TYPO3\FLOW3\Reflection\ReflectionService
	 * @api
	 * @FLOW3\Inject
	 */
	protected $reflectionService;

	/**
	 * Initialize the Adapter
	 *
		 * */
	public function init() {
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, "Foo.ContentManagement");
	}
	
	public function getFilter($being,$selected = array()){
		$beings = $this->getBeings($being);
		$filters = array();
		foreach($beings as $being){
			$properties = $being->__properties;
			foreach($properties as $property){
				if($property->isFilter()){
					if(!isset($filters[$property->getName()]))
						$filters[$property->getName()] = new \Foo\ContentManagement\Core\Filter();

					if(isset($selected[$property->getName()]) && $selected[$property->getName()] == $property->getString()){
						$property->setSelected(true);
					}
					#$string = $property->getString();
					#if(!empty($string))
						$filters[$property->getName()]->addProperty($property);
				}
			}
		}
		return $filters;
	}
	
	/**
	 * returns the specified property of the mixed variable
	 *
	 * @param string $property 
	 * @param string $mixed 
	 * @return void
		 */
	public function getValue($property, $mixed){
		$value = null;
		try {
			if(is_object($mixed) || is_array($mixed))
				$value = \TYPO3\FLOW3\Reflection\ObjectAccess::getProperty($mixed, $property);
		} catch(\TYPO3\FLOW3\Reflection\Exception\PropertyNotAccessibleException $e) {
			var_dump($e);
		}
		return $value;
	}
}

?>