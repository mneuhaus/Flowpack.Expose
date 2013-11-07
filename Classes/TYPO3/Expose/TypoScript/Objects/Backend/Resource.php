<?php
namespace TYPO3\Expose\TypoScript\Objects\Backend;

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
 * Render a Form section using the Form framework
 */
class Resource extends \TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 */
	protected $resourcePublisher;

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public function evaluate() {
		$sortedChildTypoScriptKeys = $this->sortNestedTypoScriptKeys();

		if (count($sortedChildTypoScriptKeys) === 0) {
			return NULL;
		}

		$output = array();
		foreach ($sortedChildTypoScriptKeys as $key) {
			$output[$key] = $this->getUri($this->tsValue($key));
		}

		return $output;
	}

	public function getUri($path) {
		$matches = array();
		preg_match('#^resource://([^/]+)/Public/(.*)#', $path, $matches);
		$package = $matches[1];
		$path = $matches[2];
		return $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/' . $package . '/' . $path;

	}
}

?>