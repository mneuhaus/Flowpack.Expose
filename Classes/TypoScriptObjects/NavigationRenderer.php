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
class NavigationRenderer extends \TYPO3\TypoScript\TypoScriptObjects\CollectionRenderer {

	/**
	 * @var string
	 */
	protected $providerClass;

	/**
	 * @var array
	 */
	protected $providerOptions = array();

	/**
	 * @var array
	 */
	protected $overrideContext = array();

	public function setProviderClass($providerClass) {
		$this->providerClass = $providerClass;
	}

	public function setProviderOptions($providerOptions) {
		$this->providerOptions = $providerOptions;
	}

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
		$navigationProviderClass = $this->tsValue('providerClass');
		$navigationProvider = new $navigationProviderClass($this->tsValue('providerOptions'));

		$output = '';
		$this->numberOfRenderedNodes = 0;
		$itemName = $this->tsValue('itemName');
		if ($itemName === NULL) {
			throw new \TYPO3\TypoScript\Exception('You need to set an itemName for the CollectionRenderer at the path' . $this->path, 1342770716);
		}

		foreach ($this->tsValue('overrideContext') as $contextName => $context) {
			$this->tsRuntime->pushContext($contextName, $this->tsValue('overrideContext.' . $contextName));
		}

		foreach ($navigationProvider as $collectionElement) {
			$this->tsRuntime->pushContext($itemName, $collectionElement);
			$output .= $this->tsRuntime->render($this->path . '/itemRenderer');
			$this->tsRuntime->popContext();
			$this->numberOfRenderedNodes++;
		}

		return $output;
	}
}
?>