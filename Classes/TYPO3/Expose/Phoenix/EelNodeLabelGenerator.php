<?php
namespace TYPO3\Expose\Phoenix;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Eel Node Label Generator. Should be moved to permanent location later.
 *
 * @Flow\Scope("singleton")
 */
class EelNodeLabelGenerator implements \TYPO3\TYPO3CR\Domain\Model\NodeLabelGeneratorInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Eel\EelEvaluatorInterface
	 */
	protected $eelEvaluator;

	/**
	 * Generate a default label for a node from an Eel expression
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $node
	 * @return string
	 */
	public function getLabel(\TYPO3\TYPO3CR\Domain\Model\NodeInterface $node) {
		$contentType = $node->getContentType();
		$options = $contentType->getNodeLabelGeneratorOptions();
		$variables = array(
			'context' => new \TYPO3\Eel\FlowQuery\FlowQuery(array($node)),
			'strings' => new \TYPO3\Eel\Helper\StringHelper()
		);

		return $this->eelEvaluator->evaluate($options['expression'], new \TYPO3\Eel\Context($variables));
	}
}

?>