<?php
namespace TYPO3\Expose\Operations;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Eel".                  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Count the number of elements in the context, possibly filtering them before counting.
 */
class HasMethodOperation extends \TYPO3\Eel\FlowQuery\Operations\AbstractOperation  {

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    static protected $shortName = 'hasMethod';

    /**
     * {@inheritdoc}
     *
     * @var boolean
     */
    static protected $final = TRUE;

    /**
     * {@inheritdoc}
     *$
     * @param \TYPO3\Eel\FlowQuery\FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation
     * @return mixed|null if the operation is final, the return value
     */
    public function evaluate(\TYPO3\Eel\FlowQuery\FlowQuery $flowQuery, array $arguments) {
        if (count($arguments) == 1) {
            $value = $flowQuery->getContext();
            return method_exists($value[0], $arguments[0]);
        } else {
            $flowQuery->pushOperation('hasMethod', array());
            $flowQuery->pushOperation('filter', $arguments);
        }
    }
}

?>