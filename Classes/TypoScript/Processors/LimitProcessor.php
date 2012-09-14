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
class LimitProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {
	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Pagination');
		$this->tsRuntime = $runtime;
		$context = $runtime->getCurrentContext();
		if (isset($context['objects'])) {
			$limit = $this->getLimit();
			$runtime->pushContext('objects', $context['objects']->getQuery()->setLimit($limit)->execute());
		}
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