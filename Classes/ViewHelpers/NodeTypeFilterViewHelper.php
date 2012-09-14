<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * TODO: throw this VH away once we have eel in Fluid
 *
 */
class NodeTypeFilterViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $node
     * @param string $nodeType
     */
    public function render(\TYPO3\TYPO3CR\Domain\Model\NodeInterface $node, $nodeType) {
        return $node->getChildNodes($nodeType);
    }

}

?>