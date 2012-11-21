<?php
namespace TYPO3\Expose\TypoScriptObjects\Resource;

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
class ResourceHandler extends \TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation {
	/**
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 * @Flow\Inject
	 */
	protected $resourcePublisher;

	/**
	 * Evaluate the collection nodes
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$resources = array();

		foreach ($this->subElements as $key => $value) {
			if ($key == 'stylesheets' || $key == 'javascripts') {
				$files = $value;
				if (is_array($value)) {
					foreach ($files as $name => $resource) {
						$value = $this->processPath($name, $resource, $this->path . '/' . $key);
						$value['file'] = $this->resolveResource($value['file']);
						$resources[$key][$name] = $value;
					}
				}
				$resources[$key] = \TYPO3\Expose\Utility\Arrays::sortPositionalArray($resources[$key]);
			}
		}

		$output = '';
		foreach ($resources as $type => $resources) {
			$this->tsRuntime->pushContext('resources', $resources);
			$output .= $this->tsRuntime->render($this->path . '/' . lcfirst($type) . 'Renderer') . chr(10);
			$this->tsRuntime->popContext();
		}

		return $output;
	}

	public function processPath($key, $value, $path) {
		if (isset($value['__eelExpression'])) {
			$result = $this->tsRuntime->evaluateProcessor($key, $this, $value);
		} elseif (isset($value['__objectType'])) {
			$result = $this->tsRuntime->evaluate($path . '/' . $key);
		} else {
			$result = $value;
		}
		return $result;
	}

	public function resolveResource($resource) {
		if (preg_match('#resource://([^/]*)/Public/(.*)#', $resource, $matches) > 0) {
			$package = $matches[1];
			$path = $matches[2];

			$resource = $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/' . ($package === NULL ? $this->controllerContext->getRequest()->getControllerPackageKey() : $package ) . '/' . $path;
		}
		return $resource;
	}
}

?>