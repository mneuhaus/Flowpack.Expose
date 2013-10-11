<?php
namespace TYPO3\Expose\ViewHelpers;

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
use TYPO3\TypoScript\TypoScriptObjects\Helpers\TypoScriptPathProxy;

/**
 */
class ControllerLinkViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {
	/**
	 * @var \TYPO3\Flow\Cache\CacheManager
	 * @Flow\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Initialize arguments
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Render the link.
	 *
	 * @param mixed $controller the fully qualified class name of the controller being linked, or the controller object itself
	 * @param string $type
	 * @param array $arguments
	 * @param string $action
	 * @return string The rendered link
	 * @api
	 */
	public function render($controller, $type = NULL, $arguments = array(), $action = 'index') {
		$arguments = $this->convertTypoScriptPathProxy($arguments);

		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($type !== NULL) {
			$arguments['type'] = $type;
		}

		$cache = $this->cacheManager->getCache('TYPO3_Expose_ControllerPartsCache');
		$identifier = sha1($controller);

		if (!$cache->has($identifier)) {
			if (is_string($controller)) {
				$controller = $this->objectManager->get($controller);
			}
			$request = new \TYPO3\Flow\Mvc\ActionRequest($this->controllerContext->getRequest());
			$request->setControllerObjectName(get_class($controller));
			$cache->set($identifier, array(
				'controllerName' => $request->getControllerName(),
				'controllerPackageKey' => $request->getControllerPackageKey(),
				'controllerSubpackageKey' => $request->getControllerSubpackageKey()
			));
		}
		$controllerParts = $cache->get($identifier);

		$uri = $uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor($action, $arguments, $controllerParts['controllerName'], $controllerParts['controllerPackageKey'], $controllerParts['controllerSubpackageKey']);
		$this->tag->addAttribute('href', $uri);
		$this->tag->addAttribute('class', 'btn');
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}

	public function convertTypoScriptPathProxy($input) {
		if (is_object($input) && $input instanceof TypoScriptPathProxy) {
			$output = array();
			foreach ($input as $key => $value) {
				$output[$key] = $this->convertTypoScriptPathProxy($value);
			}
			return $output;
		}
		if (is_array($input)) {
			$output = array();
			foreach ($input as $key => $value) {
				$output[$key] = $this->convertTypoScriptPathProxy($value);
			}
			return $output;
		}
		return $input;
	}
}

?>