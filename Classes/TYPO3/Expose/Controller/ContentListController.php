<?php
namespace TYPO3\Expose\Controller;

/* *
 * This script belongs to the TYPO3.Expose package.              *
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

use TYPO3\Flow\Annotations as Flow;

/**
 * Feature to display a list of TYPO3CR content nodes
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ContentListController extends AbstractController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContentTypeManager
	 */
	protected $contentTypeManager;

	/**
	 * List objects, all being of the same $type.
	 *
	 * @param string $type
	 * @param string $format
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode
	 * @return void
	 */
	public function indexAction($type = NULL, $format = 'list', \TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode = NULL, $recursiveLevels = INF) {
		$siteNode = $this->getSiteNode();

		if (class_exists('\TYPO3\TYPO3CR\Domain\Service\ContentTypeManager')) {
			$this->contentTypeManager = $this->objectManager->get('\TYPO3\TYPO3CR\Domain\Service\ContentTypeManager');
		}

		if ($selectedFolderNode === NULL && $siteNode->getPrimaryChildNode() !== NULL) {
				// No node selected, so we select the Site node and then the first child of the site node.
				// This is the root-page then.
			$selectedFolderNode = $siteNode->getPrimaryChildNode()->getPrimaryChildNode();
		}

		$this->view->assign('format', $format);
		$this->view->assign('siteNode', $siteNode);
		$this->view->assign('selectedFolderNode', $selectedFolderNode);

		if ($selectedFolderNode !== NULL) {
			$query = new \TYPO3\Expose\TYPO3CR\Persistence\Node\Query($selectedFolderNode);
			$query->setRecursiveLevels($recursiveLevels);

			if ($type !== NULL) {
				$query->matching($query->equals('contentType', $this->contentTypeManager->getContentType($type)));
			}

			$this->view->assign('objects', $query->execute());
		}
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeInterface
	 */
	protected function getSiteNode() {
		$workspaceName = $this->securityContext->getParty()->getPreferences()->get('context.workspace');
		return $this->propertyMapper->convert('/sites@' . $workspaceName, 'TYPO3\TYPO3CR\Domain\Model\NodeInterface');
	}
}
?>