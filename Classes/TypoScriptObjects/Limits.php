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
class Limits extends \TYPO3\TypoScript\TypoScriptObjects\FluidRenderer {
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
        
        $limits = array();

        foreach ($this->settings['Limits'] as $limit) {
            $limits[$limit] = false;
        }

        $limit = $this->getLimit();
        $total = $this->totalObjects();
        
        $unset = false;
        foreach ($limits as $key => $value) {
            $limits[$key] = $limit == $key;
            if (!$unset && intval($key) >= intval($total)) {
                $unset = true;
                continue;
            }
            if ($unset) {
                unset($limits[$key]);
            }
        }

        if (count($limits) == 1) {
            $limits = array();
        }

        $this->variables["limits"] = $limits;
        
        return parent::evaluate();
    }

    public function totalObjects() {
    	$objects = $this->tsRuntime->evaluateProcessor('objects', $this, $this->variables["objects"]);
        
        if (is_object($objects)) {
    	   $objects = $objects->getQuery()->setLimit(NULL)->setOffset(NULL)->execute();
    	   return $objects->count();
        }

        return 0;
    }

    public function getCurrentPage() {
    	$request = $this->tsRuntime->getControllerContext()->getRequest();

    	if($request->hasArgument('page')){
    		return $request->getArgument('page');
    	}

    	return 1;
    }

    public function getLimit() {
    	$request = $this->tsRuntime->getControllerContext()->getRequest();

    	if($request->hasArgument('limit')){
    		return $request->getArgument('limit');
    	}

    	return $this->settings['Default'];
    }
}

?>