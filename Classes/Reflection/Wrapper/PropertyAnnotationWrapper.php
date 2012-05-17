<?php
namespace Foo\ContentManagement\Reflection\Wrapper;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 */
class PropertyAnnotationWrapper extends AbstractAnnotationWrapper {
	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	public function getLabel() {
		return ucfirst($this->get("property"));
	}

	public function getType() {
		preg_match("/<(.+)>/", $this->get("type"), $matches);
		if(!empty($matches)){
			return ltrim($matches[1],"\\");
		}else{
			return strval($this->get("type"));
		}
	}

	public function isRelationProperty() {
		#return $this->containsKey("manyToMany") || 
		return $this->containsKey("manyToOne");
	}

	public function setProperty($property) {
		$this->set("property", $property);
	}

	public function getWidget() {
		$raw = strval($this->getType());
		
		$widget = null;
		$default = "TYPO3.Form:SingleLineText";
		
		$mappings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, "Foo.ContentManagement.Mapping.Widgets");
		
		if( ! empty($mappings) ) {
			if($this->has("Widget"))
				$widget = strval($this->get("Widget"));
			
			if( $widget === null && isset($mappings[$raw]) ) {
				$widget = $mappings[$raw];
			}
			
			if( $widget === null && isset($mappings[strtolower($raw)]) ) {
				$widget = $mappings[$raw];
			}
			
			if( $widget === null && isset($mappings[ucfirst($raw)]) ) {
				$widget = $mappings[$raw];
			}
			
			if( $widget === null){
				foreach($mappings as $pattern => $widget) {
					if( preg_match("/" . $pattern . "/", $raw) > 0 ) {
						break;
					}
				}
			}
		}
		
		if( $widget === null && $default !== null )
			$widget = $default;
		
		if($widget === null)
			$widget = $raw;
		
		return $widget;
	}
}

?>