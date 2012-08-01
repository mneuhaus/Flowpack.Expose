<?php
namespace Foo\ContentManagement\NavigationProvider;

/*                                                                       *
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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * NavigationProvider to show entities which are marked to be shown through
 * the annotation CM\Active seperated into groups
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EntityOverviewNavigationProvider extends AbstractNavigationProvider {

    /**
     * Constructor to load the entities grouped into the provider
     *
     * @param array $options An array of options for this provider
     * @param \Foo\ContentManagement\Core\PersistentStorageService $persistentStorageService to get the entities
     * @return void
     */
    public function __construct($options, \Foo\ContentManagement\Core\PersistentStorageService $persistentStorageService) {
        $groups = $persistentStorageService->getGroups();
        foreach ($persistentStorageService->getGroups() as $groupTitle => $group) {
            $group['title'] = $groupTitle;
            $this->items[] = $group;
        }
    }

}

?>