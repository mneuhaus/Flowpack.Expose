<?php
namespace Foo\ContentManagement\Controller;

/* *
 * This script belongs to the Foo.ContentManagement package.              *
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
class NewController extends \Foo\ContentManagement\Core\Features\FeatureController {
	/**
	 * @var \Foo\ContentManagement\Core\MetaPersistenceManager
     * @FLOW3\Inject
	 */
	protected $persistenceService;
	
	/**
	 * Function to return the Actions to be displayed for this context
	 */
	public function getActionsForContext($class, $action, $id) {
		$actions = array();
		if($action == "list" && !$id)
			$actions[] = "index";

		return $actions;
	}

	public function getShortcut(){
		return "n";
	}

	/**
	 * Create objects
	 *
	 */
	public function indexAction() {
		$being = $this->request->getArgument("being");
		$object = new $being();
		$this->view->assign("object", $object);
	}

	public function create($formRuntime) {
		$formValues = $formRuntime->getFormState()->getFormValues();
		$object = $formValues["item"];
		$this->persistenceService->add($object);

		$class = get_class($object);
		$this->redirect("index", "List", null, array( "being" => $class ));
	}
}
?>