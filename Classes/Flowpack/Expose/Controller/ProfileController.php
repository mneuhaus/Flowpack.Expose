<?php
namespace Flowpack\Expose\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "CE.Reports".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use TYPO3\Flow\Utility\Algorithms;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * @Flow\Scope("singleton")
 */
class ProfileController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var string
	 */
	protected $entity;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @return void
	 */
	protected function initializeActionMethodArguments() {
		parent::initializeActionMethodArguments();
		if (isset($this->arguments['entity'])) {
			$this->arguments['entity']->setDataType($this->entity);
			$this->arguments['entity']->getPropertyMappingConfiguration()->allowAllProperties();
		}
	}

	/**
	 * @param object $entity
	 * @return void
	 */
	public function editAction($entity) {
		if ($this->securityContext->getParty() !== $entity) {
			$this->redirect('index');
		}
		$this->view->assign('entity', $entity);
		$this->view->assign('fieldsets', $this->getFieldsets());
		$this->view->assign('className', $this->entity);
	}

	/**
	 * @param object $entity
	 * @param string $password
	 * @return void
	 */
	public function updateAction($entity, $password = NULL) {
		if ($this->securityContext->getParty() !== $entity) {
			$this->redirect('index');
		}

		if (strlen($password) > 0) {
			$credentialsSource = $this->hashService->hashPassword($password);
			$entity->getAccounts()->current()->setCredentialsSource($credentialsSource);
			$this->persistenceManager->update($entity->getAccounts()->current());
		}

		$this->persistenceManager->update($entity);
		$this->addFlashMessage('Updated the entity.');
		$this->redirect('index');
	}
}
?>