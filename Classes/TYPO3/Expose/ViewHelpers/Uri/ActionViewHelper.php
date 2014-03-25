<?php
namespace TYPO3\Expose\ViewHelpers\Uri;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * An URI ViewHelper to link to an Expose sub request using a main request
 *
 * @api
 */
class ActionViewHelper extends AbstractViewHelper {

	/**
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('mainAction', 'string', 'Name of the main request\'s action', TRUE);
		$this->registerArgument('mainController', 'string', 'Name of the main request\'s controller', TRUE);
		$this->registerArgument('mainPackage', 'string', 'Name of the main request\'s package', TRUE);
		$this->registerArgument('action', 'string', 'Name of the Expose action', FALSE);
		$this->registerArgument('controller', 'string', 'Name of the Expose controller', FALSE);
		$this->registerArgument('package', 'string', 'Name of the Expose package', FALSE);
		$this->registerArgument('mainArguments', 'string', 'Main request\'s arguments', FALSE, array());
		$this->registerArgument('arguments', 'string', 'Expose arguments', FALSE, array());
		$this->registerArgument('type', 'string', 'The Expose type', FALSE);
	}

	/**
	 * @throws ViewHelper\Exception
	 * @return string the rendered form
	 */
	public function render() {
		$uriBuilder = $this->controllerContext->getUriBuilder();

		$controllerArguments = $this->arguments['mainArguments'];
		if ($this->arguments['action'] !== NULL) {
			$controllerArguments['--expose']['@action'] = $this->arguments['action'];
		}
		if ($this->arguments['controller'] !== NULL) {
			$controllerArguments['--expose']['@controller'] = $this->arguments['controller'];
		}
		if ($this->arguments['package'] !== NULL) {
			$controllerArguments['--expose']['@package'] = $this->arguments['package'];
		}
		if ($this->arguments['type'] !== NULL) {
			$controllerArguments['--expose']['type'] = $this->arguments['type'];
		}
		$uriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE);
		try {
			$uri = $uriBuilder->uriFor($this->arguments['mainAction'], $controllerArguments, $this->arguments['mainController'], $this->arguments['mainPackage']);
		} catch (\Exception $exception) {
			throw new ViewHelper\Exception($exception->getMessage(), $exception->getCode(), $exception);
		}
		return $uri;
	}
}
