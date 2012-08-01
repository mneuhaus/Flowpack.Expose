<?php
namespace Foo\ContentManagement\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the FLOW3 package "TypoScript".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Render a TypoScript navigation collection
 *
 * //tsPath collection *Collection
 * //tsPath itemRenderer the TS object which is triggered for each element in the node collection
 */
class CollectionRenderer extends \TYPO3\TypoScript\TypoScriptObjects\CollectionRenderer {
	/**
	 * @var array
	 */
	protected $overrideContext = array();

	public function setOverrideContext($overrideContext) {
		$this->overrideContext = $overrideContext;
	}

	/**
	 * Evaluate the collection nodes
	 *
	 * @param mixed $context
	 * @return string
	 */
	public function evaluate($context) {
		foreach ($this->tsValue('overrideContext') as $contextName => $context) {
			$this->tsRuntime->pushContext($contextName, $this->tsValue('overrideContext.' . $contextName));
		}

		return parent::evaluate($context);
	}
}
?>