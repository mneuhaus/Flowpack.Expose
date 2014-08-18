<?php
namespace Flowpack\Expose\PropertyHandler;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\PropertyHandler\AbstractPropertyHandler;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Cryptography\HashService;

/**
 */
class PasswordWithConfirmationHandler extends AbstractPropertyHandler {
	/**
	 * @var HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * @param mixed $propertyValue
	 * @return void
	 */
	public function onSubmit($propertyValue) {
		if (strlen($propertyValue['password']) > 0) {

			if ($propertyValue['password'] !== $propertyValue['confirmation']) {
				return new \TYPO3\Flow\Validation\Error('Password doesn\'t match confirmation', 1408353010);
			}

			return $this->hashService->hashPassword($propertyValue['password'], 'default');
		}

		return $this->originalObject->getCredentialsSource();
	}
}

?>