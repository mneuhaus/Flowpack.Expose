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

use Flowpack\Expose\Annotations as Expose;
use Flowpack\Expose\Reflection\ClassSchema;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

class CrudController extends ActionController {
	/**
	 * @var string
	 */
	protected $entity;

	/**
	 * @var string
	 */
	protected $layout = 'Default';

	/**
	 * @var \TYPO3\Flow\Persistence\Repository
	 */
	protected $repository;

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @return void
	 */
	protected function initializeActionMethodArguments() {
		parent::initializeActionMethodArguments();

		if ($this->request->hasArgument('entityClassName') && $this->entity === NULL) {
			$this->entity = $this->request->getArgument('entityClassName');
		}

		if (isset($this->arguments['entity'])) {
			$this->arguments['entity']->setDataType($this->entity);
			$this->arguments['entity']->getPropertyMappingConfiguration()->allowAllProperties();
		}

		$this->schema = new ClassSchema($this->entity);
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @param \TYPO3\Flow\Mvc\View\ViewInterface $view The view to be initialized
	 * @return void
	 * @api
	 */
	protected function initializeView(\TYPO3\Flow\Mvc\View\ViewInterface $view) {
		$view->assign('className', $this->entity);
		$view->assign('layout', $this->layout);
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('entities', $this->getEntities());
		$this->view->assign('schema', $this->schema);
		$this->view->assign('controller', NULL);
	}

	public function getEntities() {
		$query = $this->persistenceManager->createQueryForType($this->entity);
		return $query->execute();
	}

	/**
	 * @Expose\Action(type="local", label="Show")
	 * @param object $entity
	 * @return void
	 */
	public function showAction($entity) {
		$this->view->assign('entity', $entity);
	}

	/**
	 * @Expose\Action(type="global", label="New")
	 * @return void
	 */
	public function newAction() {
		$entity = new $this->entity();
		$this->view->assign('entity', $entity);
		$this->view->assign('fieldsets', $this->getFieldsets());
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

	/**
	 * @Expose\Action(type="local", label="Edit")
	 * @param object $entity
	 * @return void
	 */
	public function editAction($entity) {
		$this->view->assign('entity', $entity);
		$this->view->assign('fieldsets', $this->getFieldsets());
	}

	public function getFieldsets() {
		if (empty($this->fields)) {
			return array(array(
				'name' => '',
				'fields' => $this->schema->getPropertyNames()
			));
		}
	}

	/**
	 * @param object $gitHubToken
	 * @return void
	 */
	public function updateAction($entity) {
		$this->persistenceManager->update($entity);
		$this->addFlashMessage('Updated the entity.');
		$this->redirect('index');
	}

	/**
	 * @Expose\Action(type="local", label="Delete", class="danger")
	 * @param object $entity
	 * @return void
	 */
	public function deleteAction($entity) {
		$this->persistenceManager->remove($entity);
		$this->persistenceManager->persistAll();
		$this->addFlashMessage('Deleted the entity.');
		$this->redirect('index');
	}

	/**
	 * @param array $entities
	 * @param string $batchAction
	 * @return void
	 */
	public function batchAction($entities, $batchAction) {
		return $this->forward($batchAction, NULL, NULL, array('entities' => $entities));
	}

	/**
	 * @Expose\Action(type="batch", label="Delete")
	 * @param array $entities
	 * @return void
	 */
	public function deleteBatchAction($entities) {
		foreach ($entities as $key => $entity) {
			$entities[$key] = $this->persistenceManager->getObjectByIdentifier($entity, $this->entity);
		}
		foreach ($entities as $key => $entity) {
			$this->persistenceManager->remove($entity);
		}
		$this->persistenceManager->persistAll();
		$this->addFlashMessage('Deleted the entities.');
		$this->redirect('index');
	}

	/**
	 * A special action which is called if the originally intended action could
	 * not be called, for example if the arguments were not valid.
	 *
	 * The default implementation sets a flash message, request errors and forwards back
	 * to the originating action. This is suitable for most actions dealing with form input.
	 *
	 * @return string
	 * @api
	 */
	protected function errorAction() {
		// $this->addErrorFlashMessage();
		$this->redirectToReferringRequest();

		return $this->getFlattenedValidationErrorMessage();
	}

}