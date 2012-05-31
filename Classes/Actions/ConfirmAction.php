<?php

namespace Foo\ContentManagement\Actions;

/* *
 * This script belongs to the FLOW3 framework.                            *
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
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ConfirmAction extends \Foo\ContentManagement\Core\Actions\AbstractAction {

	// TODO: (SK) can this even work when it always returns FALSE?
	// 		 (MN) yes, because this only decides wheter a Button/Action for it
	// 		      is shown somewhere. This action gets called through redirect
	// 		      by the DeleteAction in Line 85 to confirm the Deletion
	public function canHandle($being, $action = null, $id = false) {
		return false;
	}

	public function getShortcut(){
		return "c";
	}

	/**
	 *
	 * @param string $being
	 * @param array $ids
	 * @author Marc Neuhaus <mneuhaus@famelo.com>
	 * */
	public function execute($being, $ids = null) {
		$objects = array();

		foreach ($ids as $id) {
			$objects[] = $this->adapter->getObject($being, $id);
		}
		$this->view->assign("objects", $objects);
		$this->view->assign("ids", implode(",", $ids));
		$this->view->assign("class", $this->contentManager->getClassShortName($being));
		$this->actionManager->getView()->setTemplateByAction("confirm");
	}

}
?>