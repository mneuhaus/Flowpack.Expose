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
 * Feature to display a list of TYPO3CR content nodes
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ContentListController extends \TYPO3\Admin\Core\AbstractAdminController {

    /**
     * List objects, all being of the same $type.
     *
     * TODO: Filtering of this list, bulk
     *
     * @param string $format
     * @param TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode
     */
    public function indexAction($format = 'table', \TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode = NULL) {
        $siteNode = $this->getSiteNode();
        $this->view->assign('format', $format);
        $this->view->assign('siteNode', $siteNode);
        $this->view->assign('selectedFolderNode', $selectedFolderNode);
        if ($selectedFolderNode !== NULL) {
            $contentNodes = $this->getContentElements($selectedFolderNode, TRUE);
            $this->view->assign('objects', $contentNodes);
        }
    }

	/**
     *
     * !!! RECURSIVE FUNCTION
     *
     * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $node
     * @param boolean $recursive
     * @return array
     */
    protected function getContentElements(\TYPO3\TYPO3CR\Domain\Model\NodeInterface $node = NULL, $recursive) {
        if ($node === NULL) {
            return array();
        }
        $contentTypeFilter = NULL;
        if ($recursive === FALSE) {
            $contentTypeFilter = '!TYPO3.TYPO3:Page';
        }
        $childNodes = $node->getChildNodes($contentTypeFilter);
        $result = $childNodes;
        foreach ($childNodes as $childNode) {
            $result = array_merge($result, $this->getContentElements($childNode, $recursive));
        }
        return $result;
    }

    /**
     * @return \TYPO3\TYPO3CR\Domain\Model\NodeInterface
     */
    protected function getSiteNode() {
        return $this->propertyMapper->convert('/sites', 'TYPO3\\TYPO3CR\\Domain\\Model\\NodeInterface');
    }
}
?>