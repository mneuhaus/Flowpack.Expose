<?php
namespace TYPO3\Expose\Phoenix;

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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Eel Node Label Generator. Should be moved to permanent location lateron.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */

/**
 * A default Node label generator
 */
class EelNodeLabelGenerator implements \TYPO3\TYPO3CR\Domain\Model\NodeLabelGeneratorInterface {

    /**
     * @FLOW3\Inject
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
        $variables = array('context' => new \TYPO3\Eel\FlowQuery\FlowQuery(array($node
        	)),
        	'strings' => new \TYPO3\Eel\Helper\StringHelper()
        );
        return $this->eelEvaluator->evaluate($options['expression'], new \TYPO3\Eel\Context($variables));
    }

}

?>