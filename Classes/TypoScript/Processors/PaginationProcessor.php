<?php
namespace TYPO3\Expose\TypoScript\Processors;

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
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 *
 */
class PaginationProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {
	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Pagination');
		$this->tsRuntime = $runtime;
		$this->context = $runtime->getCurrentContext();
		if (isset($this->context['objects'])) {
			$offset = $this->getOffset();
			$runtime->pushContext('objects', $this->context['objects']->getQuery()->setOffset($offset)->execute());
		}
	}

	public function getOffset() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		$page = 1;
		if ($request->hasArgument('page')) {
			$page = $request->getArgument('page');
		}

		$offset = $this->getLimit() * ($page -1);

		$total = $this->context["objects"]->count();
		if($offset > $total){
			$pages = ceil($total / $this->getLimit());
			$offset = $this->getLimit() * ( $pages - 1 );
		}

		return $offset;
	}

	public function getLimit() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		
		$limit = $this->settings["Default"];
		if ($request->hasArgument('limit')) {
			$limit = $request->getArgument('limit');
		}

		return $limit;
	}

	public function process($subject) {
		return $subject;
	}

	public function afterInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		if (isset($context['objects'])) {
			$runtime->popContext();
		}
	}
}
?>