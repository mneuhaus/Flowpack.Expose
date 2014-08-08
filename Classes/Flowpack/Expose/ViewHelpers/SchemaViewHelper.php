<?php
namespace Flowpack\Expose\ViewHelpers;

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
		$schema = new \Flowpack\Expose\Schema\DefaultSchema($className);
		$this->templateVariableContainer->add($as, $schema);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $content;
	}
}