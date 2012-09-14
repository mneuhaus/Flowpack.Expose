<?php
namespace TYPO3\Expose\TypoScriptObjects;

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
 *
 */
class Search extends \TYPO3\TypoScript\TypoScriptObjects\FluidRenderer {
	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

    /**
     * @return string
     */
    public function evaluate() {
    	$this->settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Pagination');

        $request = $this->tsRuntime->getControllerContext()->getRequest();
        if ($request->hasArgument("search")) {
            $this->variables["search"] = $request->getArgument("search");
        }
        
        return parent::evaluate();
    }
}

?>