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
 * Action to create a new Being
 *
 */
class NewController extends AbstractController {

	/**
	 * Create a new object
	 *
	 * @param string $type
	 * @return void
	 */
	public function indexAction($type) {
		$objects = array(new $type());
		$this->view->assign('className', $type);
		$this->view->assign('objects', $objects);
		$this->view->assign('callback', 'create');
	}

	/**
	 * @param string $type
	 */
	public function createAction($type) {
		$objects = $this->request->getInternalArgument('__objects');
		foreach ($objects as $object) {
			$this->persistenceManager->add($object);
		}
		$this->redirect('index', 'sametypelist', 'TYPO3.Expose', array('type' => $type));
	}

}

?>