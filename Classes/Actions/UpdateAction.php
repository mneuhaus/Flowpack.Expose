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
 * Action to Update the Being
 *
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("prototype")
 */
class UpdateAction extends \Foo\ContentManagement\Core\Actions\AbstractAction {

	/**
	 * Function to Check if this Requested Action is supported
	 * @author Marc Neuhaus <mneuhaus@famelo.com>
	 * */
	public function canHandle($being, $action = null, $id = false) {
		switch($action) {
			case "bulk":
			case "update":
			case "confirm":
			case "create":
				return false;
			default:
				return $id;
		}
	}

	/**
	 * The Name of this Action
	 * @author Marc Neuhaus <mneuhaus@famelo.com>
	 * */
	public function __toString() {
		return "Edit";
	}
	
	public function getShortcut(){
		return "e";
	}
	
	/**
	 * Edit objects
	 *
	 * @param string $class
	 * @param array $ids
	 * @author Marc Neuhaus <mneuhaus@famelo.com>
	 * */
	public function execute($class, $ids = null) {
		$object = $this->adapter->getObject($class, current($ids));
		$this->view->assign("object", $object);
	}

	public function formFinischer($formRuntime) {
		$request = $formRuntime->getRequest();
		$values = $formRuntime->getFormState()->getFormValues();
		$values["__identity"] = $request->getArgument("id");
		$class = \Foo\ContentManagement\Core\API::get("classShortNames", $request->getArgument("being"));
		$id = $request->getArgument("id");
		$this->adapter->updateObject($class, $id, $values["item"]);

		$this->actionManager->redirect("list", array("being" => $request->getArgument("being")));
	}
}
?>