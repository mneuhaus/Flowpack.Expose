<?php
namespace TYPO3\Expose\Form;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Form".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A processing Rule contains information for property mapping and validation.
 *
 * **This class is not meant to be subclassed by developers.**
 */
class ProcessingRule extends \TYPO3\Form\Core\Model\ProcessingRule {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Expose\Form\PropertyMappingConfiguration
	 */
	protected $propertyMappingConfiguration;

}
?>