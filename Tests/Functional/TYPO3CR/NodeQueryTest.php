<?php
namespace TYPO3\Expose\Tests\Functional\TYPO3CR;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3CR".                    *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * Functional test case which tests the rendering
 *
 * @group large
 */
class NodeQueryTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @var boolean
	 */
	protected $testableHttpEnabled = TRUE;

	/**
	 * @var boolean
	 */
	protected $testableSecurityEnabled = TRUE;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
	 */
	protected $node;

	public function setUp() {
		parent::setUp();
		$nodeRepository = $this->objectManager->get('TYPO3\TYPO3CR\Domain\Repository\NodeRepository');
		\TYPO3\FLOW3\Reflection\ObjectAccess::setProperty($nodeRepository, 'context', new \TYPO3\TYPO3\Domain\Service\ContentContext('live'), TRUE);
		$siteImportService = $this->objectManager->get('TYPO3\TYPO3\Domain\Service\SiteImportService');
		$siteImportService->importSitesFromFile(__DIR__ . '/Fixtures/NodeStructure.xml');
		$this->persistenceManager->persistAll();

		$propertyMapper = $this->objectManager->get('TYPO3\FLOW3\Property\PropertyMapper');
		$this->node = $propertyMapper->convert('/sites/example/home', 'TYPO3\TYPO3CR\Domain\Model\Node');
		$this->assertFalse($propertyMapper->getMessages()->hasErrors());
	}

	/**
	 * @test
	 */
	public function nodeQueryReturnsNodes() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$nodes = $query->execute();
		$this->assertTrue(count($nodes) > 0);
	}

	/**
	 * @test
	 */
	public function nodeQueryReturnsNodesFromPath() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$query->setParentPath("/");
		$this->assertEquals($query->count(), 3, "There are only 3 Nodes on the Path '/' got " . $query->count() . "!");

		$query->setParentPath("teaser");
		$this->assertEquals($query->count(), 1, "There is only 1 Nodes on the Path '/teaser' got " . $query->count() . "!");
	}

	/**
	 * @test
	 */
	public function nodeQueryAppliesLimits() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$query->setLimit(2);
		$this->assertEquals($query->count(), 2, "I limited it to return 2 Nodes, yet i got " . $query->count() . "!");
	}

	/**
	 * @test
	 */
	public function nodeQueryAppliesOffsets() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$query->setLimit(2);
		$query->setOffset(2);
		$this->assertEquals($query->count(), 1, "With a Limit of 2 and an offset of 2 i should get 1 Node, because there are 3 in total, but i got " . $query->count() . "!");
	}

	/**
	 * @test
	 */
	public function fetchNodesRecursively() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$query->setRecursiveLevels(INF);
		$this->assertEquals($query->count(), 15);

		$query->setRecursiveLevels(2);
		$this->assertEquals($query->count(), 12);
	}

	/**
	 * @test
	 */
	public function contrainNodesByProperties() {
		$query = $this->objectManager->get('TYPO3\Expose\TYPO3CR\Persistence\Node\Query', $this->node);
		$query->setRecursiveLevels(INF);
		$query->matching($query->like("title", "Last Commits"));
		$node = $query->execute()->getFirst();
		$this->assertEquals($node->getProperty("title"), "Last Commits", "node: " . $node->getName());

		$query->matching($query->like("title", "Last%mits"));
		$node = $query->execute()->getFirst();
		$this->assertEquals($node->getProperty("title"), "Last Commits", "node: " . $node->getName());
	}
}
?>