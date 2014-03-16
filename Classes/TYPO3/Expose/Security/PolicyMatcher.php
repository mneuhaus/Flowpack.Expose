<?php
namespace TYPO3\Expose\Security;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\RequestMatcher;

/**
 *
 */
class PolicyMatcher extends RequestMatcher {

	/**
	 * The securityContext
	 *
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $typoScriptPath;

	/**
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
	 * @param \TYPO3\Flow\Mvc\RequestMatcher $parentMatcher
	 * @param \TYPO3\Flow\Aop\JoinPoint $joinPoint
	 */
	public function __construct(\TYPO3\Flow\Mvc\ActionRequest $actionRequest = NULL, $parentMatcher = NULL, $joinPoint = NULL) {
		$this->request = $actionRequest;
		$this->parentMatcher = $parentMatcher;

		if ($joinPoint === NULL) {
			return;
		}

		if ($joinPoint->isMethodArgument('type')) {
			$this->type = $joinPoint->getMethodArgument('type');
		} else if ($this->request->hasArgument('type')) {
			$this->type = $this->request->getArgument('type');
		}

		if (method_exists($joinPoint->getProxy(), 'getTypoScriptPath')) {
			$this->typoScriptPath = $joinPoint->getProxy()->getTypoScriptPath();
		}
	}

	/**
	 * @param string $className
	 * @return boolean
	 */
	public function isType($className) {
		if ($this->type === $className) {
			$this->addWeight(100000);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param string $typoScriptPath
	 * @return boolean
	 */
	public function isTypoScriptPath($typoScriptPath) {
		if (preg_match('#' . $typoScriptPath . '#', $this->typoScriptPath) === 1) {
			$this->addWeight(100000);
			return TRUE;
		}

		return FALSE;
	}

}
?>