<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
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