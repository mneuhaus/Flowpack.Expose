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
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * Action to display the list and apply Bulk aktions and filter if necessary
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ListController extends \Foo\ContentManagement\Core\Actions\AbstractAction {

	/**
	 * List objects
	 */
	public function indexAction() {
		if($this->request->hasArgument("being")){
			$this->being = $this->persistentStorageService->getClassShortName($this->request->getArgument("being"));
			$this->view->assign('className', $this->being);

			$this->settings = $this->getSettings();
			
			$this->persistentStorageService->initQuery($this->being);
			$results = $this->persistentStorageService->getQuery($this->being)->execute();
			$this->view->assign("objects", $results);
			
			// Redirect to creating a new Object if there aren't any (Clean Slate)
			if( $results->count() < 1 ) {
				$arguments = array("being" => $this->persistentStorageService->getClassShortName($this->being));
				$this->redirect("create", NULL, NULL, $arguments);
			}
			
			$listActions = $this->actionManager->getActions("list", $this->being, true);
			$this->view->assign('listActions', $listActions);

			$hasId = isset($this->id) ? true : false;
			$topBarActions = $this->actionManager->getActions("list", $this->being, $hasId);
			$this->view->assign('topBarActions',$topBarActions);
			
			return $this->handleBulkActions();
		}
	}
	
	public function handleBulkActions(){
		$actions = $this->actionManager->getActions("bulk", $this->being, true);
		$this->view->assign("bulkActions", $actions);

		$request = $this->request;
		do{
			if ($request->hasArgument("bulkAction"))
				break;
			$request = $request->getParentRequest();
		} while ($request->getParentRequest() instanceof ActionRequest);

		if( $request->hasArgument("bulkAction") ) {
			$bulkAction = $request->getArgument("bulkAction");
			if( isset($actions[$bulkAction]) ) {
				$action = $actions[$bulkAction];

				$this->actionManager->getView()->setTemplateByAction($action->getAction());

				if($action->getAction() !== $bulkAction)
					$action = $this->actionManager->getActionByShortName($action->getAction() . "Action");
				
				$action->execute($this->being, $request->getArgument("bulkItems"));
				return $action->view->render();
			}
		}
	}
}
?>