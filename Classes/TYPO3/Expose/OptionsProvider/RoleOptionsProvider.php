<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * OptionsProvider for related Beings
 *
 */
class RoleOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

    /**
     * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
     * @FLOW3\Inject
     */
    protected $configurationManager;

    /**
    * TODO: Document this Method! ( getOptions )
    */
    public function getOptions() {
        $acls = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_POLICY, 'acls');
        return array_keys($acls);
    }

}

?>