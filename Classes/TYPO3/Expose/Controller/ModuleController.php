<?php
namespace TYPO3\Expose\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * Module controller for the Expose package; main entry point when this package is
 * used inside Phoenix.
 */
class ModuleController extends \TYPO3\TYPO3\Controller\Module\StandardController {

	/**
	 * Index action
	 *
	 * @return string
	 */
	public function indexAction() {
		$exposeRuntime = new \TYPO3\Expose\Core\ExposeRuntime($this->request, $this->response);
		if (isset($this->moduleConfiguration['defaultExposeControllerClassName'])) {
			$exposeRuntime->setDefaultExposeControllerClassName($this->moduleConfiguration['defaultExposeControllerClassName']);
		}
		if (isset($this->moduleConfiguration['defaultExposeControllerArguments'])) {
			$exposeRuntime->setDefaultExposeControllerArguments($this->moduleConfiguration['defaultExposeControllerArguments']);
		}
		return $exposeRuntime->execute();
	}

}

?>