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
final class Value implements SingleAnnotationInterface {
	/**
	 * @var mixed
	 */
	public $value = '';

	/**
	 * @param mixed $value
	 */
	public function __construct(array $values) {
		if (isset($values['value'])) {
			$this->value = $values['value'];
		}
	}
	
	public function __toString(){
		return $this->value;
	}

	public function getValue() {
		return $this->value();
	}
}

?>