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
class NewController extends \Foo\ContentManagement\Core\Features\AbstractFeature {

	public function isFeatureRelatedForContext($context, $type = NULL) {
		if (in_array($context, array('List'))) {
			return 500;
		}
		return FALSE;
	}

	public function getName() {
		return 'New';
	}


	/**
	 * Create objects
	 *
	 */
	public function indexAction() {
		$being = $this->persistentStorageService->getClassShortName($this->request->getArgument("being"));
		$object = new $being();
		$this->view->assign("object", $object);
	}

	public function create($formRuntime) {
		$formValues = $formRuntime->getFormState()->getFormValues();
		$object = $formValues["item"];
		$class = get_class($object);
		$this->persistentStorageService->createObject($class, $object);

		$this->redirect("index", "List", null, array( "being" => $class ));
	}
}
?>