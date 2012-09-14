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
class Pagination extends \TYPO3\TypoScript\TypoScriptObjects\FluidRenderer {
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
        $this->addPaginationVariables();
        return parent::evaluate();
    }

    public function addPaginationVariables() {
    	$currentPage = $this->getCurrentPage();

        $pages = array();
        for ($i = 0; $i < $this->totalObjects() / $this->getLimit(); $i++) {
            $pages[] = $i + 1;
        }

        if ($currentPage > count($pages)) {
            $currentPage = count($pages);
        }

        if (count($pages) > 1) {
            $this->variables['currentPage'] = $currentPage;
            if ($currentPage < count($pages)) {
                $this->variables['nextPage'] = $currentPage + 1;
            }
            if ($currentPage > 1) {
                $this->variables['previousPage'] = $currentPage - 1;
            }
            if (count($pages) > $this->settings['MaxPages']) {
                $max = $this->settings['MaxPages'];
                $start = $currentPage - ($max + $max % 2) / 2;
                $start = $start > 0 ? $start : 0;
                $start = $start > 0 ? $start : 0;
                $start = $start + $max > count($pages) ? count($pages) - $max : $start;
                $pages = array_slice($pages, $start, $max);
            }
            $this->variables['pages'] = $pages;
        }
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