<?php
namespace Flowpack\Expose\Reflection\Sources;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\Sources\AbstractSchemaSource;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Utility\Arrays;

/**
 */
class YamlSource extends AbstractSchemaSource {

	/**
	 * @Flow\Inject
	 * @var ConfigurationManager
	 */
	protected $configurationManager;

	public function compileSchema() {
		$schema = (array) $this->configurationManager->getConfiguration('Expose', $this->className);
		$arrayKeys = array(
			'listProperties',
			'searchProperties',
			'filterProperties'
		);
		foreach ($arrayKeys as $key) {
			if (isset($schema[$key]) && is_string($schema[$key])) {
				$schema[$key] = Arrays::trimExplode(',', $schema[$key]);
			}
		}
		return $schema;
	}
}

?>