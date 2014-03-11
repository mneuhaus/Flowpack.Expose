<?php
namespace TYPO3\Expose\Security;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Features".       *
 *                                                                        *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Eel\Context;
use TYPO3\Expose\Security\PolicyMatcher;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Exception;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class PolicyService {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Eel\CompilingEvaluator
	 */
	protected $eelEvaluator;

	/**
	 * @var array
	 */
	protected $runtimeCache = array();

	public function getPrivilegesForJoinPoint(\TYPO3\Flow\Security\Policy\Role $role, \TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$joinPointIdenifier = $this->getJoinPointIdenifier($joinPoint) . $role->getIdentifier();
		if (!isset($this->runtimeCache[$joinPointIdenifier])) {
			$this->runtimeCache[$joinPointIdenifier] = NULL;

			$policies = $this->configurationManager->getConfiguration('Policy', 'resources.expose');

			$acls = $this->configurationManager->getConfiguration('Policy', 'acls');
			if (!isset($acls[$role->getIdentifier()]) || !isset($acls[$role->getIdentifier()]['expose'])) {
				$this->runtimeCache[$joinPointIdenifier] = array();
				return array();
			}
			$acls = $acls[$role->getIdentifier()]['expose'];

			$request = ObjectAccess::getProperty($joinPoint->getProxy(), 'request', TRUE);
			$policyMatcher = new PolicyMatcher($request, NULL, $joinPoint);
			$context = new Context($policyMatcher);

			$privileges = array();
			$counter = 0;
			foreach ($acls as $name => $privilege) {
				$policyMatcher->resetWeight();
				$policy = $policies[$name];
				$result = $this->eelEvaluator->evaluate($policy, $context);
				switch ($privilege) {
						case 'GRANT':
							$privilege = \TYPO3\Flow\Security\Policy\PolicyService::PRIVILEGE_GRANT;
							break;
						case 'DENY':
							$privilege = \TYPO3\Flow\Security\Policy\PolicyService::PRIVILEGE_DENY;
							break;
						case 'ABSTAIN':
							$privilege = \TYPO3\Flow\Security\Policy\PolicyService::PRIVILEGE_ABSTAIN;
							break;
						default:
							throw new \TYPO3\Flow\Security\Exception\InvalidPrivilegeException('Invalid privilege defined in security policy. An ACL entry may have only one of the privileges ABSTAIN, GRANT or DENY, but we got "' . $privilege . '" for role "' . $role->getIdentifier() . '" and resource "' . $name . '"', 1367311437);
					}
				$privileges[$policyMatcher->getWeight() + $counter] = array(
					'name' => $name,
					'privilege' => $privilege,
					'matches' => $result
				);
			}
			$this->runtimeCache[$joinPointIdenifier] = $privileges;
		}

		return $this->runtimeCache[$joinPointIdenifier];
	}

	protected function getJoinPointIdenifier($joinPoint) {
		$identifier = strtolower($joinPoint->getClassName() . '->' . $joinPoint->getMethodName());

		if ($joinPoint->isMethodArgument('type')) {
			$identifier .= $joinPoint->getMethodArgument('type');
		}

		if (method_exists($joinPoint->getProxy(), 'getTypoScriptPath')) {
			$identifier .= $joinPoint->getProxy()->getTypoScriptPath();
		}

		return $identifier;
	}
}
?>