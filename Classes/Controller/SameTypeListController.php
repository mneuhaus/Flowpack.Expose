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
use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * Action to display a list of records of the same type
 *
 */
class SameTypeListController extends AbstractController {

	/**
	 * List objects, all being of the same $type.
	 *
	 * TODO: Filtering of this list, bulk
	 *
	 * @param string $type
	 * @param string $format
	 * @return void
	 */
	public function indexAction($type, $format = 'table') {
		if ($type === 'TYPO3\TYPO3CR\Domain\Model\NodeInterface') {
				// If we deal with nodes, we want the content list controller to take over
			$this->forward('index', 'contentlist', 'TYPO3.Expose', $this->request->getArguments());
		}

		$query = $this->persistenceManager->createQueryForType($type);
		$objects = $query->execute();
		$this->redirectToNewFormIfNoObjectsFound($objects);
		$this->view->assign('type', $type);
		$this->view->assign('format', $format);
		$this->view->assign('objects', $objects);
	}

	/**
	 * @param \TYPO3\FLOW3\Persistence\QueryResultInterface $result
	 * @return void
	 */
	protected function redirectToNewFormIfNoObjectsFound(\TYPO3\FLOW3\Persistence\QueryResultInterface $result) {
		if (count($result) === 0) {
			$arguments = array('type' => $this->arguments['type']->getValue());
			$this->redirect('index', 'new', NULL, $arguments);
		}
	}

}

?>