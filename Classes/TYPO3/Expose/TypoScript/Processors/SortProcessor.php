<?php
namespace TYPO3\Expose\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 */
class SortProcessor extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {
	/**
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$object = $this->tsValue('objects')->getFirst();
		if (!is_object($object)) {
			return $this->tsValue('objects');
		}

		$request = $this->tsRuntime->getControllerContext()->getRequest();

		if ($request->hasArgument('sort')) {
			return $this->tsValue('objects')
				 ->getQuery()
				 ->setOrderings($request->getArgument('sort'))
				 ->execute();
		}

		return $this->tsValue('objects');
	}
}
?>