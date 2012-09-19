<?php
namespace TYPO3\Expose\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * Feature to display a list of TYPO3CR content nodes
 *
 */
class ContentListController extends AbstractController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Security\Context
	 */
	protected $securityContext;

	/**
	 * List objects, all being of the same $type.
	 *
	 * TODO: Filtering of this list, bulk
	 *
	 * @param string $format
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode
	 * @return void
	 */
	public function indexAction($format = 'list', \TYPO3\TYPO3CR\Domain\Model\NodeInterface $selectedFolderNode = NULL) {
		$siteNode = $this->getSiteNode();
		$this->view->assign('format', $format);
		$this->view->assign('siteNode', $siteNode);
		$this->view->assign('selectedFolderNode', $selectedFolderNode);

		if ($selectedFolderNode !== NULL) {
			$query = new \TYPO3\Expose\TYPO3CR\Persistence\Node\Query($selectedFolderNode);
			$query->setRecursiveLevels(INF);
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