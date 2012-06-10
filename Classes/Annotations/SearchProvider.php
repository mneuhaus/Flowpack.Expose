<?php
namespace Foo\ContentManagement\Annotations;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * @Annotation
 */
final class SearchProvider implements SingleAnnotationInterface {
	
	/**
	 * @var integer
	 */
	public $name = "";
	
	/**
	 * @var array
	 */
	public $options = array();
	
	/**
	 * @param string $value
	 */
	public function __construct(array $values = array()) {
		foreach ($values as $key => $value) {
			$this->$key = $value;
		}
		$this->name = isset($values['value']) ? $values['value'] : $this->name;
		$this->name = isset($values['name']) ? $values['name'] : $this->name;
		
		$this->options = isset($values['options']) ? $values['options'] : $this->options;
		
		if(class_exists(sprintf("\\Foo\\ContentManagement\\SearchProvider\\%sSearchProvider", $this->name)))
			$this->name = sprintf("\\Foo\\ContentManagement\\SearchProvider\\%sSearchProvider", $this->name);
	}
	
	public function __toString(){
		return $this->name;
	}
}

?>
