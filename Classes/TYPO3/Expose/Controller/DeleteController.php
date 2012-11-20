<?php
namespace TYPO3\Expose\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
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
 * Action to confirm the deletion of a being
 *
 */
class DeleteController extends AbstractController {
	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
		$this->arguments['objects']->getPropertyMappingConfiguration()->allowAllProperties();
	}

	/**
	 * delete objects
	 *
	 * @param string $type
	 * @param \Doctrine\Common\Collections\Collection $objects
	 * @return void
	 */
	public function indexAction($type, $objects) {
		$this->view->assign('className', $type);
		$this->view->assign('objects', $objects);
	}


	/**
	 * @return void
	 */
	public function initializeDeleteAction() {
		$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
		$this->arguments['objects']->getPropertyMappingConfiguration()->allowAllProperties();
	}

	/**
	 * delete objects
	 *
	 * @param string $type
	 * @param \Doctrine\Common\Collections\Collection $objects
	 * @return void
	 */
	public function deleteAction($type, $objects) {
		if ($type === 'TYPO3\TYPO3CR\Domain\Model\NodeInterface') {
			foreach ($objects as $object) {
				$object->remove();
			}
		} else {
			foreach ($objects as $object) {
				$this->persistenceManager->remove($object);
			}
		}
		$this->persistenceManager->persistAll();

		$this->redirect('index', 'sametypelist', 'TYPO3.Expose', array('type' => $type));
	}
}

?>