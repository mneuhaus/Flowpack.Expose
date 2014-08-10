<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;


/**
 * This ViewHelper helps you to use the currently logged in party in your fluid template without littering every controller
 * the securityContext and assigning the current party to the view.
 */
class UserViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 *
	 * @param string $currentUser
	 * @return string
	 */
	public function render($as = 'currentUser') {
		$templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
		$templateVariableContainer->add($as, $this->securityContext->getParty());
		$output = $this->renderChildren();
		$templateVariableContainer->remove($as);
		return $output;
	}
}