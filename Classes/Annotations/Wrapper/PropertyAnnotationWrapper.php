<?php
namespace Foo\ContentManagement\Annotations\Wrapper;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class PropertyAnnotationWrapper extends AbstractAnnotationWrapper {
	public function getLabel() {
		return ucfirst($this->get("property"));
	}

	public function getOptionsProvider() {
		return (string) current($this->get("optionsProvider"));
	}

	public function getType() {
		preg_match("/<(.+)>/", current($this->get("var")), $matches);
		if(!empty($matches)){
			return ltrim($matches[1],"\\");
		}else{
			return current($this->get("var"));
		}
	}

	public function isRelationProperty() {
		#return $this->containsKey("manyToMany") || 
		return $this->containsKey("manyToOne");
	}

	public function setProperty($property) {
		$this->set("property", $property);
	}
}

?>