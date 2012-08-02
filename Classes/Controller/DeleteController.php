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
 * Action to confirm the deletion of a being
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DeleteController extends \Foo\ContentManagement\Core\Features\FeatureController {
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
		if(!in_array($action, array("view", "bulk", "update", "confirm", "create")) && $id == true)
			$actions[] = "index";

		return $actions;
	}

    /**
     * @return string
     */
    public function getShortcut(){
		return "c";
	}

	/**
	 *
	 */
	public function indexAction() {
		$class = $this->request->getArgument("being");
		
		$ids = array();
		if($this->request->hasArgument("id"))
			$ids = array( $this->request->getArgument("id") );
		else if($this->request->hasArgument("ids"))
			$ids = $this->request->getArgument("ids");
		
		if(count($ids) > 0){
			$objects = array();
			foreach ($ids as $id) {
				$objects[] = $this->persistenceService->getObjectByIdentifier($id, $class);
			}
			$this->view->assign("objects", $objects);
			$this->view->assign("ids", implode(",", $ids));
			$this->view->assign("class", $class);
		}
	}

	/**
	 * Delete objects
	 *
	 */
	public function deleteAction() {
		$class = $this->request->getArgument("being");
		
		$ids = array();
		if($this->request->hasArgument("id"))
			$ids = array( $this->request->getArgument("id") );
		else if($this->request->hasArgument("ids"))
			$ids = $this->request->getArgument("ids");

		if( is_array($ids) ) {
			foreach($ids as $id) {
				$object = $this->persistenceService->getObjectByIdentifier($id, $class);
				$this->persistenceService->remove($object);
			}
			
			$arguments = array("being" => $class);
			$this->redirect('index', "list", null, $arguments);
		}
	}
}
?>