<?php
namespace TYPO3\Expose\Neos;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class ExposePluginImplementation extends \TYPO3\Neos\TypoScript\PluginImplementation {
	/**
	 * Build the pluginRequest object
	 *
	 * @return ActionRequest
	 */
	protected function buildPluginRequest() {
		$pluginRequest = parent::buildPluginRequest();

		$pluginRequest->setArgument('type', $this->node->getProperty('type'));
		$pluginRequest->setArgument('controller', $this->node->getProperty('controller'));
		$tsPrefix = $this->node->getProperty('tsPrefix');
		if (strlen($tsPrefix) > 0) {
			$pluginRequest->setArgument('__typoScriptPrefix', $tsPrefix);
		}

		return $pluginRequest;
	}
}
