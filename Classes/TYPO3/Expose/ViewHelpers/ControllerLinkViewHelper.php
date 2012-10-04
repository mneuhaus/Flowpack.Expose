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

/**
 */
class ControllerLinkViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

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
	 * @return string The rendered link
	 * @api
	 */
	public function render($controller, $type = NULL, array $arguments = array()) {
		if (is_string($controller)) {
			$controller = $this->objectManager->get($controller);
		}
		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($type !== NULL) {
			$arguments['type'] = $type;
		}

		$request = new \TYPO3\Flow\Mvc\ActionRequest($this->controllerContext->getRequest());
		$request->setControllerObjectName(get_class($controller));
		$uri = $uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('index', $arguments, $request->getControllerName(), $request->getControllerPackageKey(), $request->getControllerSubpackageKey());
		$this->tag->addAttribute('href', $uri);
		$this->tag->addAttribute('class', 'btn');
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}
}

?>