<?php
namespace Flowpack\Expose\ViewHelpers\Link;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\Fluid\Core\ViewHelper\Exception;

/**
 */
class FilterViewHelper extends AbstractTagBasedViewHelper {
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
		$this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
		$this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
		$this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
	}

	/**
	 * Render the link.
	 *
	 * @param string $property Property to sort by
	 * @param string $value value to sort by
	 * @param string $action Target action
	 * @param array $arguments Arguments
	 * @param string $controller Target controller. If NULL current controllerName is used
	 * @param string $package Target package. if NULL current package is used
	 * @param string $subpackage Target subpackage. if NULL current subpackage is used
	 * @param string $section The anchor to be added to the URI
	 * @param string $format The requested format, e.g. ".html"
	 * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @param boolean $useParentRequest If set, the parent Request will be used instead of the current one
	 * @param boolean $absolute By default this ViewHelper renders links with absolute URIs. If this is FALSE, a relative URI is created instead
	 * @return string The rendered link
	 * @throws Exception
	 * @api
	 */
	public function render($property, $value = NULL, $action = NULL, $arguments = array(), $controller = NULL, $package = NULL, $subpackage = NULL, $section = '', $format = '',  array $additionalParams = array(), $addQueryString = TRUE, array $argumentsToBeExcludedFromQueryString = array(), $useParentRequest = FALSE, $absolute = TRUE) {
		$filter = array();
		if ($this->viewHelperVariableContainer->exists('Flowpack\Expose\Processor\FilterProcessor', 'filter')) {
			$filter = $this->viewHelperVariableContainer->get('Flowpack\Expose\Processor\FilterProcessor', 'filter');
		}
		$request = $this->controllerContext->getRequest();

		$action = $request->getControllerActionName();

		if ($request->hasArgument("filter")) {
			$arguments['filter'] = $request->getArgument("filter");
		} else {
			$arguments['filter'] = array();
		}


		if ($value === NULL) {
			unset($arguments['filter'][$property]);
		} else {
			$arguments['filter'][$property] = $value;
		}
		$argumentsToBeExcludedFromQueryString[] = 'filter';

		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($useParentRequest) {
			$request = $this->controllerContext->getRequest();
			if ($request->isMainRequest()) {
				throw new Exception('You can\'t use the parent Request, you are already in the MainRequest.', 1360163536);
			}
			$uriBuilder = clone $uriBuilder;
			$uriBuilder->setRequest($request->getParentRequest());
		}

		$uriBuilder
			->reset()
			->setSection($section)
			->setCreateAbsoluteUri($absolute)
			->setArguments($additionalParams)
			->setAddQueryString($addQueryString)
			->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
			->setFormat($format);
		try {
			$uri = $uriBuilder->uriFor($action, $arguments, $controller, $package, $subpackage);
		} catch (\Exception $exception) {
			throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
		}

		$this->tag->addAttribute('href', $uri);

		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		$class = 'filter';

		if ((isset($filter[$property]) && $filter[$property] == $value) || ($value === NULL && !isset($filter[$property]))) {
			$class .= ' active';
		}

		if ($this->tag->hasAttribute('class')) {
			$class = $this->tag->getAttribute('class', $class) . ' ' . $class;
		}

		$this->tag->addAttribute('class', $class);

		return $this->tag->render();
	}
}

?>