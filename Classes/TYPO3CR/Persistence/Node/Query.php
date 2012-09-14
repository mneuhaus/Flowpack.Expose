<?php
namespace TYPO3\Expose\TYPO3CR\Persistence\Node;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A Query class for Nodes
 *
 * @api
 */
class Query extends \TYPO3\FLOW3\Persistence\Doctrine\Query {
	/**
	 * current parentPath
	 * 
	 * @var string
	 */
	protected $parentPath = NULL;

	/**
	 * Amount of Levels to go down recursively to fetch childNodes
	 * 
	 * @var integer
	 */
	protected $recursiveLevels = 0;

	/**
	 * @var \TYPO3\FLOW3\Persistence\Generic\Qom\QueryObjectModelFactory
	 */
	protected $qomFactory;

    /**
     * Constructs a query object working on the given type
     *
     * @param \TYPO3\TYPO3CR\Domain\Model\Node $rootNode
     */
    public function __construct(\TYPO3\TYPO3CR\Domain\Model\NodeInterface $rootNode) {
        $this->rootNode = $rootNode;
        $this->entityClassName = '\\TYPO3\\TYPO3CR\\Domain\\Model\\Node';
    }

	/**
	 * Injects the FLOW3 QOM factory
	 *
	 * @param \TYPO3\FLOW3\Persistence\Generic\Qom\QueryObjectModelFactory $qomFactory
	 * @return void
	 */
	public function injectQomFactory(\TYPO3\FLOW3\Persistence\Generic\Qom\QueryObjectModelFactory $qomFactory) {
		$this->qomFactory = $qomFactory;
	}

    /**
     * Contrain this query to a parentPath
     *
     * @param string $parentPath
     * @return object
     */
    public function setParentPath($parentPath) {
        $this->parentPath = $parentPath;
    }

    /**
     * Executes the query and returns the result.
     *
     * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The query result
     * @api
     */
    public function execute() {
        return new QueryResult($this);
    }
	
	/**
	 * Returns the query result count
	 *
	 * @return integer The query result count
	 * @throws \TYPO3\FLOW3\Persistence\Doctrine\DatabaseConnectionException
	 * @api
	 */
	public function count() {
		return count($this->getResult());
	}

	/**
	 * Gets the results of this query as array.
	 *
	 * Really executes the query on the database.
	 * This should only ever be executed from the QueryResult class.
	 *
	 * @return array result set
	 * @throws \TYPO3\FLOW3\Persistence\Doctrine\DatabaseConnectionException
	 */
	public function getResult() {
		$node = $this->rootNode;

		if ($this->parentPath !== NULL && $this->parentPath !== "/") {
			$node = $this->rootNode->getNode($this->parentPath);
		}

		$nodes = $this->getChildNodes($node);

		$nodes = $this->filterNodes($nodes);

		$nodes = array_slice($nodes, $this->getOffset(), $this->getLimit());

		return $nodes;
	}

	/**
	 * fetch childNodes of a node respecting the recursiveLevels
	 * 
	 * @param  object  $rootNode
	 * @param  integer $level
	 * @return array   $nodes
	 */
	public function getChildNodes($rootNode, $level = 0) {
		if (is_infinite($this->recursiveLevels) || $level < $this->recursiveLevels) {
			$nodes = array();
			foreach($rootNode->getChildNodes() as $node){
				$nodes[] = $node;
				$nodes = array_merge($nodes, $this->getChildNodes($node, $level++));	
			}
		}else {
			$nodes = $rootNode->getChildNodes();
		}
		return $nodes;
	}

	/**
	 * Filter nodes based on current constraints
	 * 
	 * @param  array $nodes
	 * @return array $nodes
	 */
	public function filterNodes($nodes) {
		$matchingNodes = array();
		if (is_object($this->constraint)) {
			foreach($nodes as $node) {
				switch (get_class($this->constraint)) {
					case 'TYPO3\FLOW3\Persistence\Generic\Qom\Comparison':
						$property = $this->constraint->getOperand1()->getPropertyName();
						$comparison = strtolower($this->constraint->getOperand2());

						if ($property == "*"){
							$properties = $node->getPropertyNames();
						} else {
							$properties = array($property);
						}

						foreach($properties as $property){
							if (!$node->hasProperty($property)) {
								continue;
							}

							$value = strtolower($node->getProperty($property));

							switch($this->constraint->getOperator()) {
								case \TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_EQUAL_TO:
										if ($value == $comparison) {
											$matchingNodes[] = $node;
										}
									break;

								case \TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_LIKE:
									$comparison = preg_quote($comparison);
									$comparison = str_replace("%", ".+", $comparison);
									$comparison = str_replace("?", ".", $comparison);
									if (preg_match("/" . $comparison . "/", $value)) {
										$matchingNodes[] = $node;
									}
									break;
							}
						}
						break;
					
					default:
						throw new \TYPO3\TYPO3\Exception('Currently on Comparisons are supported for Node Queries', 1346761586);
						break;
				}
			}
			return array_unique($matchingNodes);
		}
		return $nodes;
	}

	/**
	 * Set the amount of Levels to fetch childs from a node
	 * 
	 * @param int|INF $levels
	 */
	public function setRecursiveLevels($levels) {
		$this->recursiveLevels = $levels;
	}

	/**
	 * The constraint used to limit the result set. Returns $this to allow
	 * for chaining (fluid interface)
	 *
	 * @param \TYPO3\FLOW3\Persistence\Generic\Qom\Constraint $constraint
	 * @return \TYPO3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function matching($constraint) {
		$this->constraint = $constraint;
		return $this;
	}

	/**
	 * Returns an equals criterion used for matching objects against a query.
	 *
	 * It matches if the $operand equals the value of the property named
	 * $propertyName. If $operand is NULL a strict check for NULL is done. For
	 * strings the comparison can be done with or without case-sensitivity.
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param mixed $operand The value to compare with
	 * @param boolean $caseSensitive Whether the equality test should be done case-sensitive for strings
	 * @return object
	 * @todo Decide what to do about equality on multi-valued properties
	 * @api
	 */
	public function equals($propertyName, $operand, $caseSensitive = TRUE) {
		if ($operand === NULL) {
			$comparison = $this->qomFactory->comparison(
				$this->qomFactory->propertyValue($propertyName, '_entity'),
				\TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_IS_NULL
			);
		} elseif (is_object($operand) || $caseSensitive) {
			$comparison = $this->qomFactory->comparison(
				$this->qomFactory->propertyValue($propertyName, '_entity'),
				\TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_EQUAL_TO,
				$operand
			);
		} else {
			$comparison = $this->qomFactory->comparison(
				$this->qomFactory->lowerCase(
					$this->qomFactory->propertyValue($propertyName, '_entity')
				),
				\TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_EQUAL_TO,
				strtolower($operand)
			);
		}

		return $comparison;
	}

	/**
	 * Returns a like criterion used for matching objects against a query.
	 * Matches if the property named $propertyName is like the $operand, using
	 * standard SQL wildcards.
	 *
	 * @param string $propertyName The name of the property to compare against
	 * @param string $operand The value to compare with
	 * @param boolean $caseSensitive Whether the matching should be done case-sensitive
	 * @return object
	 * @throws \TYPO3\FLOW3\Persistence\Exception\InvalidQueryException if used on a non-string property
	 * @api
	 */
	public function like($propertyName, $operand, $caseSensitive = TRUE) {
		if (!is_string($operand)) {
			throw new \TYPO3\FLOW3\Persistence\Exception\InvalidQueryException('Operand must be a string, was ' . gettype($operand), 1276781107);
		}
		if ($caseSensitive) {
			$comparison = $this->qomFactory->comparison(
				$this->qomFactory->propertyValue($propertyName, '_entity'),
				\TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_LIKE,
				$operand
			);
		} else {
			$comparison = $this->qomFactory->comparison(
				$this->qomFactory->lowerCase(
					$this->qomFactory->propertyValue($propertyName, '_entity')
				),
				\TYPO3\FLOW3\Persistence\QueryInterface::OPERATOR_LIKE,
				strtolower($operand)
			);
		}

		return $comparison;
	}
}

?>