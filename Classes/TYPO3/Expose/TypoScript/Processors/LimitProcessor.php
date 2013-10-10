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
 * Render a Form section using the Form framework
 */
class LimitProcessor extends \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject {

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function evaluate() {
		$objects = $this->tsValue('objects');
		return 3;
	}

	/**
	 * @param \TYPO3\TypoScript\Core\Runtime $runtime
	 * @param \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject
	 * @param string $typoScriptPath
	 * @return void
	 */
	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject $typoScriptObject, $typoScriptPath) {
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Pagination');
		$this->tsRuntime = $runtime;
		$context = $runtime->getCurrentContext();
		if (isset($context['objects'])) {
			$limit = $this->getLimit();
			$runtime->pushContext('objects', $context['objects']->getQuery()->setLimit($limit)->execute());
		}
	}

	/**
	 * @return integer
	 */
	public function getLimit() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		$limit = $this->settings['Default'];
		if ($request->hasArgument('limit')) {
			$limit = $request->getArgument('limit');
		}

		return $limit;
	}

}

?>