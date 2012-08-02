<?php
namespace Foo\ContentManagement\ViewHelpers;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 *
 */
class FeatureLinkViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 * @FLOW3\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

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
	 * @param mixed $feature the fully qualified class name of the feature being linked, or the feature class itself
	 * @param string $type
	 * @param object $object the object to link to
	 * @param array $arguments
	 * @return string The rendered link
	 * @api
	 */
	public function render($feature, $type = NULL, $object = NULL, $arguments = array()) {
		if (is_string($feature)) {
			$feature = $this->objectManager->get($feature);
		}

		$uriBuilder = $this->controllerContext->getUriBuilder();

		if ($type !== NULL) {
			$arguments['type'] = $type;
		}

		if ($object !== NULL) {
			$arguments['identifier'] = $this->persistenceManager->getIdentifierByObject($object);
		}

		$featureClassName = get_class($feature);

		$request = new \TYPO3\FLOW3\Mvc\ActionRequest($this->controllerContext->getRequest());
		$request->setControllerObjectName($featureClassName);

		$uri = $uriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->uriFor('index', $arguments, $request->getControllerName(), $request->getControllerPackageKey(), $request->getControllerSubpackageKey());
		$this->tag->addAttribute('href', $uri);
		$this->tag->addAttribute('class', 'btn');

		$this->tag->setContent($feature->getName());
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}
}
?>