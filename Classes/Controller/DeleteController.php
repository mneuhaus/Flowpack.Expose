<?php
namespace TYPO3\Expose\Controller;

/* *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Action to confirm the deletion of a being
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DeleteController extends \TYPO3\Expose\Core\AbstractExposeController {

	public function initializeAction() {
		$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
		$this->arguments['objects']->getPropertyMappingConfiguration()->allowAllProperties();
	}

	/**
	 * delete objects
	 *
	 * @param string $type
	 * @param Doctrine\Common\Collections\Collection $objects
	 */
	public function indexAction($type, $objects) {
		$this->view->assign('className', $type);
		$this->view->assign('objects', $objects);
	}

	/**
	 * delete objects
	 *
	 * @param string $type
	 * @param Doctrine\Common\Collections\Collection $objects
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

		$this->redirect('index', 'sametypelist', 'TYPO3.Expose', array('type' => $type));
	}
}

?>