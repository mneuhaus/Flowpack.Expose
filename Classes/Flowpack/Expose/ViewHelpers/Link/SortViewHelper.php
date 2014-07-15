<?php
namespace Flowpack\Expose\ViewHelpers\Link;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 */
class SortViewHelper extends AbstractTagBasedViewHelper {
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
	 * @throws ViewHelper\Exception
	 * @api
	 */
	public function render($property, $action = NULL, $arguments = array(), $controller = NULL, $package = NULL, $subpackage = NULL, $section = '', $format = '',  array $additionalParams = array(), $addQueryString = TRUE, array $argumentsToBeExcludedFromQueryString = array(), $useParentRequest = FALSE, $absolute = TRUE) {
		$sorting = $this->viewHelperVariableContainer->get('Flowpack\Expose\ViewHelpers\Query\SortViewHelper', 'sorting');
		$request = $this->controllerContext->getRequest();

		$action = $request->getControllerActionName();

		$arguments['sortBy'] = $property;
		$arguments['order'] = $sorting['oppositeOrder'];

		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($useParentRequest) {
			$request = $this->controllerContext->getRequest();
			if ($request->isMainRequest()) {
				throw new ViewHelper\Exception('You can\'t use the parent Request, you are already in the MainRequest.', 1360163536);
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
			throw new ViewHelper\Exception($exception->getMessage(), $exception->getCode(), $exception);
		}

		$this->tag->addAttribute('href', $uri);

		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		$class = 'sort';

		if ($property == $sorting['sortBy'] && $sorting['order'] == 'ASC') {
			$class .= ' sort-asc';
		} else if ($property == $sorting['sortBy'] && $sorting['order'] == 'DESC') {
			$class .= ' sort-desc';
		}

		if ($this->tag->hasAttribute('class')) {
			$class = $this->tag->getAttribute('class', $class) . ' ' . $class;
		}

		$this->tag->addAttribute('class', $class);

		return $this->tag->render();
	}
}

?>