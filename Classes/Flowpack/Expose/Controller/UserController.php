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
class UserController extends AbstractAuthenticationController {
	/**
	 * The default view object to use if none of the resolved views can render
	 * a response for the current request.
	 *
	 * @var string
	 * @api
	 */
	protected $defaultViewObjectName = 'Flowpack\Expose\View\FallbackView';

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
	 *
	 *
	 * @return string
	 */
	public function indexAction() {
		if ($this->securityContext->getParty() instanceof \TYPO3\Party\Domain\Model\AbstractParty) {
			$this->redirectToUri('/');
		}
	}

	/**
	 * Authenticates an account by invoking the Provider based Authentication Manager.
	 *
	 * On successful authentication redirects to the list of posts, otherwise returns
	 * to the login screen.
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Security\Exception\AuthenticationRequiredException
	 */
	public function authenticateAction() {
		try {
			$this->authenticationManager->authenticate();
			$this->addFlashMessage('Login successul!');
			$this->redirect('index');
		} catch (\TYPO3\Flow\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Wrong username or password.');
			throw $exception;
		}
	}

	/**
	 * Redirects to a potentially intercepted request. Returns an error message if there has been none.
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
	 * @return string
	 */
	protected function onAuthenticationSuccess(\TYPO3\Flow\Mvc\ActionRequest $originalRequest = NULL) {
		if ($originalRequest !== NULL) {
			$this->redirectToRequest($originalRequest);
		}
		return 'There was no redirect implemented and no intercepted request could be found after authentication.
				Please implement onAuthenticationSuccess() in your login controller to handle this case correctly.
				If you have a template for the authenticate action, simply make sure that onAuthenticationSuccess()
				returns NULL in your login controller.';
	}

	/**
	 *
	 * @return void
	 */
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->addFlashMessage('Successfully logged out.');
		$this->redirectToUri('/');
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

	public function getFieldsets() {
		if (empty($this->fields)) {
			return array(array(
				'name' => '',
				'fields' => $this->reflectionService->getClassPropertyNames($this->entity)
			));
		}
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