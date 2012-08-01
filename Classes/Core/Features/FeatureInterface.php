<?php

namespace Foo\ContentManagement\Core\Features;

/* *
 * This script belongs to the Foo.ContentManagement package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Interface for features such as "Create", "List", "Delete"
 *
 * Each feature can place itself at certain so-called "Contexts", which are linking points provided by other features.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
interface FeatureInterface {
	/**
	 * Determine if this feature is relevant in another context like "List" or "List.Element"
	 * @param type $context
	 * @param type $type
	 * @return integer 0 or FALSE if feature is not related; numeric value determines the sorting.
	 */
	public function isFeatureRelatedForContext($context, $type = NULL);

//	public function indexAction($type = NULL, $identifier = NULL);


//	public function getName();
}
?>