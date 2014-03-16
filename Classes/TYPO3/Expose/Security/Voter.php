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

use TYPO3\Expose\Controller\ExposeControllerInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * An access decision voter, that asks the Flow PolicyService for a decision.
 *
 * @Flow\Scope("singleton")
 */
class Voter implements \TYPO3\Flow\Security\Authorization\AccessDecisionVoterInterface {

	/**
	 * The policy service
	 * @var \TYPO3\Expose\Security\PolicyService
	 */
	protected $policyService;

	/**
	 * Constructor.
	 *
	 * @param \TYPO3\Expose\Security\PolicyService $policyService The policy service
	 */
	public function __construct(\TYPO3\Expose\Security\PolicyService $policyService) {
		$this->policyService = $policyService;
	}

	/**
	 * This is the default Policy voter, it votes for the access privilege for the given join point
	 *
	 * @param \TYPO3\Flow\Security\Context $securityContext The current security context
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The joinpoint to vote for
	 * @return integer One of: VOTE_GRANT, VOTE_ABSTAIN, VOTE_DENY
	 */
	public function voteForJoinPoint(\TYPO3\Flow\Security\Context $securityContext, \TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		if (!$joinPoint->getProxy() instanceof ExposeControllerInterface) {
			return self::VOTE_ABSTAIN;
		}

		if ($this->isActionMethod($joinPoint->getMethodName()) === FALSE) {
			return self::VOTE_GRANT;
		}

		$accessGrants = 0;
		$accessDenies = 0;
		foreach ($securityContext->getRoles() as $role) {
			try {
				$privileges = $this->policyService->getPrivilegesForJoinPoint($role, $joinPoint);
			} catch (\TYPO3\Flow\Security\Exception\NoEntryInPolicyException $e) {
				return self::VOTE_ABSTAIN;
			}

			foreach ($privileges as $privilege) {
				if ($privilege['matches'] === FALSE) {
					continue;
				}
				if ($privilege['privilege'] === \TYPO3\Flow\Security\Policy\PolicyService::PRIVILEGE_GRANT) {
					$accessGrants++;
				} elseif ($privilege['privilege'] === \TYPO3\Flow\Security\Policy\PolicyService::PRIVILEGE_DENY) {
					$accessDenies++;
				}
			}
		}

		if ($accessDenies > 0) {
			return self::VOTE_DENY;
		}
		if ($accessGrants > 0) {
			return self::VOTE_GRANT;
		}

		return self::VOTE_ABSTAIN;
	}

	/**
	 * @param string $methodName
	 */
	public function isActionMethod($methodName) {
		if (substr($methodName, -6) !== 'Action') {
			return FALSE;
		}
		if (substr($methodName, 0, 10) === 'initialize') {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * This is the default Policy voter, it votes for the access privilege for the given resource
	 *
	 * @param \TYPO3\Flow\Security\Context $securityContext The current security context
	 * @param string $resource The resource to vote for
	 * @return integer One of: VOTE_GRANT, VOTE_ABSTAIN, VOTE_DENY
	 */
	public function voteForResource(\TYPO3\Flow\Security\Context $securityContext, $resource) {
		return self::VOTE_ABSTAIN;
	}
}
