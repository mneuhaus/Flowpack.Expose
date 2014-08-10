<?php
namespace Flowpack\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\OptionsProvider\AbstractOptionsProvider;
use TYPO3\Flow\Annotations as Flow;

/**
 * OptionsProvider for Policy Roles
 *
 */
class RoleOptionsProvider extends AbstractOptionsProvider {

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	* TODO: Document this Method! ( getOptions )
	*/
	public function getOptions() {
		$roleDefinitions = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_POLICY, 'roles');
		$roles = array();
		foreach (array_keys($roleDefinitions) as $roleName) {
			$roles[] = new \TYPO3\Flow\Security\Policy\Role($roleName);;
		}
		return $roles;
	}

}

?>