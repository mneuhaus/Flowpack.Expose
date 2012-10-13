<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class PropertiesViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

   	/**
   	 * @var \TYPO3\Flow\Reflection\ReflectionService
   	 * @Flow\Inject
   	 */
   	protected $reflectionService;

	/**
	 * @param object $object
	 * @param string $className
	 * @param string $as
	 * @return string Rendered string
	 * @api
	 */
	public function render($object = NULL, $className = NULL, $as = 'properties') {
		if (is_null($className) && !is_null($object)) {
			$className = $this->reflectionService->getClassNameByObject($object);
		}

		$schema = $this->reflectionService->getClassSchema($className);

		$properties = $schema->getProperties();

		$this->templateVariableContainer->add($as, $properties);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		return $content;
	}
}

?>