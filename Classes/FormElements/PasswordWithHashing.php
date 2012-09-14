<?php
namespace TYPO3\Expose\FormElements;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A password with confirmation form element
 */
class PasswordWithHashing extends \TYPO3\Form\FormElements\PasswordWithConfirmation {

    /**
     * @var \TYPO3\FLOW3\Security\Cryptography\HashService
     * @FLOW3\Inject
     */
    protected $hashService;

    /**
    * TODO: Document this Method! ( onSubmit )
    */
    public function onSubmit(\TYPO3\Form\Core\Runtime\FormRuntime $formRuntime, &$elementValue) {
        if ($elementValue['password'] !== $elementValue['confirmation']) {
            $processingRule = $this->getRootForm()->getProcessingRule($this->getIdentifier());
            $processingRule->getProcessingMessages()->addError(new \TYPO3\FLOW3\Error\Error('Password doesn\'t match confirmation', 1334768052));
        }
        if (empty($elementValue['password'])) {
            $elementValue = $this->getDefaultValue();
        } else {
            $elementValue = $this->hashService->hashPassword($elementValue['password'], 'default');
        }
    }

}

?>