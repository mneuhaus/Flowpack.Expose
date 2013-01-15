<?php
namespace TYPO3\Expose\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Expose command controller for the TYPO3.Expose package
 *
 * @Flow\Scope("singleton")
 */
class ExposeCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * An command to create a configuration for an Entity
	 *
	 * @param string $package Package to create the Configuration for
	 * @param string $entity Entity to create the Configuration for
	 * @param boolean $override
	 * @return void
	 */
	public function schemaCommand($package, $entity, $override = FALSE) {
		//$this->outputLine('You called the example command and passed "%s" as the first argument.', array($requiredArgument));

		$schema = file_get_contents('resource://TYPO3.Expose/Private/Kickstart/Schema.ts2');
		$replacements = array(
			'{EntityClassName}' => $package . '.Domain.Model.' . str_replace('/', '.', $entity),
			'{EntityName}' => $entity,
			'{Package}' => $package
		);
		$schema = str_replace(array_keys($replacements), array_values($replacements), $schema);

		$schemaPath = 'resource://' . $package . '/Private/TypoScripts/Schema/';
		if (!is_dir($schemaPath)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($schemaPath);
		}

		$schemaFile = $schemaPath . $entity . '.ts2';
		if (!file_exists($schemaFile) || $override) {
			file_put_contents($schemaFile, $schema);
		}

		$listPath = 'resource://' . $package . '/Private/Elements/List/' . $entity . '/';
		if (!is_dir($listPath)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($listPath);
		}

		$tableFile = $listPath . 'Table.html';
		if (!file_exists($tableFile) || $override) {
			copy('resource://TYPO3.Expose/Private/Kickstart/Elements/List/Table.html', $tableFile);
		}

		$rowFile = $listPath . 'Row.html';
		if (!file_exists($rowFile) || $override) {
			copy('resource://TYPO3.Expose/Private/Kickstart/Elements/List/Row.html', $rowFile);
		}
	}

}

?>