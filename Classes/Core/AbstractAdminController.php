<?php
namespace TYPO3\Admin\Core;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Base class for admin controllers. An admin controller implements a certain
 * functionality inside the Admin UI, such as "Edit", "New", "List" or "Delete".
 *
 * // REVIEWED for release.
 */
abstract class AbstractAdminController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {
	protected $defaultViewObjectName = 'TYPO3\\TypoScript\\View\\TypoScriptView';

    /**
	 * Most admin controllers need the Property Mapper as they work for arbitrary
	 * data types and need to perform the conversion manually. that's why we inject
	 * it here for convenience reasons.
	 *
     * @var \TYPO3\FLOW3\Property\PropertyMapper
     * @FLOW3\Inject
     */
    protected $propertyMapper;
}
?>