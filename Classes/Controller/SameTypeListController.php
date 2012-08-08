<?php
namespace TYPO3\Admin\Controller;

/* *
 * This script belongs to the TYPO3.Admin package.              *
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
 * Action to display a list of records of the same type
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class SameTypeListController extends \TYPO3\Admin\Core\AbstractAdminController {

    /**
     * List objects, all being of the same $type.
     *
     * TODO: Filtering of this list, bulk
     *
     * @param string $type
     * @param string $format
     */
    public function indexAction($type, $format = 'table') {
        $query = $this->persistenceManager->createQueryForType($type);
        $objects = $query->execute();
        $this->redirectToNewFormIfNoObjectsFound($objects);
        $this->view->assign('type', $type);
        $this->view->assign('format', $format);
        $this->view->assign('objects', $objects);
    }

    /**
    * TODO: Document this Method! ( redirectToNewFormIfNoObjectsFound )
    */
    protected function redirectToNewFormIfNoObjectsFound(\TYPO3\FLOW3\Persistence\QueryResultInterface $result) {
        if (count($result) === 0) {
            $arguments = array('type' => $this->arguments['type']->getValue()
            );
            $this->redirect('index', 'new', NULL, $arguments);
        }
    }

}

?>