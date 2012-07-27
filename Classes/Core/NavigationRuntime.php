<?php
namespace Foo\ContentManagement\Core;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * @api
 */
class NavigationRuntime extends AbstractRuntime {

	/**
	 * Default action to render if nothing else is specified 
	 * or present in the arguments
	 *
	 * @var string
	 * @internal
	 */
	protected $defaultController = "\Foo\ContentManagement\Controller\NavigationController";

	/**
	 * Default action to render if nothing else is specified 
	 * or present in the arguments
	 *
	 * @var string
	 * @internal
	 */
	protected $defaultAction = "index";

	/**
	 *
	 * @var string
	 */
	protected $namespace = "navigationRuntime";

	/**
	 *
	 * @var string
	 */
	protected $navigationProvider = "\Foo\ContentManagement\NavigationProvider\EntityNavigationProvider";

	/**
	 *
	 * @var array
	 */
	protected $context = array(
		'navigationProviderOptions' => array()
	);

	public function setNavigationListElement($navigationListElement) {
		$this->request->setArgument("__navigationListElement", $navigationListElement);
	}
}
?>