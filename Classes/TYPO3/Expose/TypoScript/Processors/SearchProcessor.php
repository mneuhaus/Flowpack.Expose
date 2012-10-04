<?php
namespace TYPO3\Expose\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 */
class SearchProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {

	/**
	 * @param \TYPO3\TypoScript\Core\Runtime $runtime
	 * @param \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject
	 * @param string $typoScriptPath
	 * @return void
	 */
	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject, $typoScriptPath) {
		$this->tsRuntime = $runtime;
		$context = $runtime->getCurrentContext();
		$search = $this->getSearch();
		if (isset($context['objects']) && $search !== NULL) {
			$query = $context['objects']->getQuery();

			$query->matching($query->like('*', $search));

			$runtime->pushContext('objects', $query->execute());
		}
	}

	/**
	 * @return string
	 */
	public function getSearch() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		if ($request->hasArgument("search")) {
			return $request->getArgument("search");
		}
		return NULL;
	}

	/**
	 * @param mixed $subject
	 * @return mixed
	 */
	public function process($subject) {
		return $subject;
	}

	/**
	 * @param \TYPO3\TypoScript\Core\Runtime $runtime
	 * @param \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject
	 * @param string $typoScriptPath
	 * @return void
	 */
	public function afterInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject, $typoScriptPath) {
		if (isset($context['objects'])) {
			$runtime->popContext();
		}
	}
}
?>