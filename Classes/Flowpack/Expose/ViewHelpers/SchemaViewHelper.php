<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Reflection\ClassSchema;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;

class SchemaViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 *
	 * @param string $className
	 * @param string $as
	 * @return string
	 */
	public function render($className, $as = 'schema') {
		$schema = new ClassSchema($className);

		$existingSchema = NULL;
		if ($this->templateVariableContainer->exists($as)) {
			$existingSchema = $this->templateVariableContainer->get($as);
			$this->templateVariableContainer->remove($as);
		}

		$this->templateVariableContainer->add($as, $schema);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		if ($existingSchema !== NULL) {
			$this->templateVariableContainer->add($as, $existingSchema);
		}
		return $content;
	}
}