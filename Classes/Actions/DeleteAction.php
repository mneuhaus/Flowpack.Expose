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
 * Action to delete a being
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DeleteAction extends \Foo\ContentManagement\Core\Actions\AbstractAction {

	/**
	 * Function to Check if this Requested Action is supported
		 * */
	public function canHandle($being, $action = null, $id = false) {
		switch($action) {
			case "view":
			case "update":
			case "confirm":
			case "create":
				return false;
			default:
				return $id;
		}
	}
	
	public function getClass() {
		return "btn danger";
	}

	public function getAction() {
		return "confirm";
	}
	
	public function getShortcut(){
		return "d";
	}
	
	/**
	 * Delete objects
	 *
	 */
	public function execute() {
		$being = $this->request->getArgument("being");
		
		$ids = array();
		if($this->request->hasArgument("id"))
			$ids = array( $this->request->getArgument("id") );
		else if($this->request->hasArgument("ids"))
			$ids = $this->request->getArgument("ids");

		if( is_array($ids) ) {
			if( $this->request->hasArgument("confirm") ) {
				foreach($ids as $id) {
					$this->adapter->deleteObject($being, $id);
				}
				
				$arguments = array("being" => $this->contentManager->getClassShortName($being));
				$this->actionManager->redirect('list', $arguments);
			}else {
				$arguments = $this->request->getArguments();
				$arguments["id"] = implode(",", $ids);
				$arguments["being"] = $being;
				$this->actionManager->redirect("confirm", $arguments);
			}
		}
	}

}
?>