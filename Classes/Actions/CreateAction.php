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
 * Action to create a new Being
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class CreateAction extends \Foo\ContentManagement\Core\Actions\AbstractAction {
	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Function to Check if this Requested Action is supported
		 * */
	public function canHandle($being, $action = null, $id = false) {
		return false;
	}

	/**
	 * Create objects
	 *
	 */
	public function execute() {
		if($this->request->hasArgument("formValues")){
			$formValues = unserialize($this->request->getArgument("formValues"));
			$object = $formValues["item"];
			$class = get_class($object);
			$result = $this->contentManager->createObject($class, $object);
			$requestHandler = new \Foo\ContentManagement\Core\RequestHandler($this->request);
			$requestHandler->redirect("list", array( "being" => $class ));
		}
	}

}
?>