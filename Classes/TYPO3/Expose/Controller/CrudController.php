<?php
namespace TYPO3\Expose\Controller;

use TYPO3\Expose\Domain\Schema;
use TYPO3\Flow\Annotations as Flow;

class CrudController extends FallbackController {
	/**
	 * @var string
	 */
	protected $entity;

	/**
	 * @var \TYPO3\Flow\Persistence\Repository
	 */
	protected $repository;

	/**
	 * @var array
	 */
	protected $listFields = array('__toString');

	/**
	 * @var array
	 */
	protected $listProcessors = array(
		'\TYPO3\Expose\Processors\SearchProcessor',
		'\TYPO3\Expose\Processors\FilterProcessor',
		'\TYPO3\Expose\Processors\PaginationProcessor',
		'\TYPO3\Expose\Processors\SortProcessor'
	);

	/**
	 * @var array
	 */
	protected $searchFields = array();

	/**
	 * @var array
	 */
	protected $filterFields = array();

	/**
	 * @var array
	 */
	protected $fields = array();

	/**
	 * @var string
	 */
	protected $defaultSortBy;

	/**
	 * @var string
	 */
	protected $defaultOrder = 'ASC';

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

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
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('entities', $this->getEntities());
		$this->view->assign('listFields', $this->listFields);
		$this->view->assign('defaultSortBy', $this->defaultSortBy);
		$this->view->assign('defaultOrder', $this->defaultOrder);
		$this->view->assign('searchFields', $this->searchFields);
		$this->view->assign('filterFields', $this->filterFields);
		$this->view->assign('listProcessors', $this->listProcessors);
	}

	public function getEntities() {
		$query = $this->persistenceManager->createQueryForType($this->entity);
		return $query->execute();
	}

	/**
	 * @param object $entity
	 * @return void
	 */
	public function showAction($entity) {
		$this->view->assign('entity', $entity);
	}

	/**
	 * @return void
	 */
	public function newAction() {
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
				'fields' => $this->reflectionService->getClassPropertyNames($this->entity)
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
	 * @param object $entity
	 * @return void
	 */
	public function deleteAction($entity) {
		$this->persistenceManager->remove($entity);
		$this->addFlashMessage('Deleted the entity.');
		$this->redirect('index');
	}

}