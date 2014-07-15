<?php
namespace Flowpack\Expose\Validation;

/**
 * Validator for passwords
 */
class PasswordValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Returns TRUE, if the given property ($value) is a valid array consistent of two equal passwords and their length
	 * is between 'minimum' (defaults to 0 if not specified) and 'maximum' (defaults to infinite if not specified)
	 * to be specified in the validation options.
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @throws \TYPO3\Flow\Validation\Exception\InvalidSubjectException
	 */
	protected function isValid($value) {
		if (!is_array($value)) {
			throw new \TYPO3\Flow\Validation\Exception\InvalidSubjectException('The given value was not an array.', 1324641197);
		}

		$password = trim(strval(array_shift($value)));
		$repeatPassword = trim(strval(array_shift($value)));

		if (strcmp($password, $repeatPassword) !== 0) {
			$this->addError('The passwords did not match.', 1324640997);
			return;
		}
	}

}

?>