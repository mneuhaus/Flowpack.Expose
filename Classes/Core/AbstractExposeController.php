<?php
namespace TYPO3\Expose\Core;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Base class for expose controllers. An expose controller implements a certain
 * functionality inside the Expose UI, such as "Edit", "New", "List" or "Delete".
 *
 * // REVIEWED for release.
 */
abstract class AbstractExposeController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {
	protected $defaultViewObjectName = 'TYPO3\\TypoScript\\View\\TypoScriptView';

    /**
	 * Most expose controllers need the Property Mapper as they work for arbitrary
	 * data types and need to perform the conversion manually. that's why we inject
	 * it here for convenience reasons.
	 *
     * @var \TYPO3\FLOW3\Property\PropertyMapper
     * @FLOW3\Inject
     */
    protected $propertyMapper;
}
?>