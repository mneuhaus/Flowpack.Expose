<?php
namespace Flowpack\Expose\Controller;

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
use TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use TYPO3\Flow\Utility\Algorithms;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * @Flow\Scope("singleton")
 */
class RegistrationController extends AbstractExposeController {
	/**
	 * @Flow\Inject(setting="Registration.PartyClassName", package="Flowpack.Expose")
	 * @var string
	 */
	protected $entity;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 */
	protected $hashService;

	/**
	 * @var boolean
	 */
	protected $requiresEmailVerification = TRUE;

	public function indexAction() {
		$entity = new $this->entity();
		$this->view->assign('entity', $entity);
		$this->view->assign('schema', $this->schema);
	}

	/**
	 * @param object $entity
	 * @return void
	 */
	public function createAction($entity) {
		$this->persistenceManager->add($entity);
		$this->addFlashMessage('Created a new entity.');
		$this->redirect('index');
	}
}
?>