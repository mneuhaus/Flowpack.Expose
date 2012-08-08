<?php
namespace TYPO3\Admin\NavigationProvider;

/*                                                                       *
* This script belongs to the TYPO3.Admin package.              *
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
     * @param \TYPO3\Admin\Reflection\AnnotationService $annotationService
     * @return void
     */
    public function __construct($options, \TYPO3\Admin\Reflection\AnnotationService $annotationService) {
        $this->annotationService = $annotationService;
        $groups = $this->getGroups();
        foreach ($groups as $groupTitle => $beings) {
            $this->items[] = array('title' => $groupTitle,
                'beings' => $beings
            );
        }
    }

    /**
    * TODO: Document this Method! ( getGroups )
    */
    public function getGroups() {
        $groups = array();
        $classes = $this->annotationService->getClassesAnnotatedWith(array('Active'
        ));
        foreach ($classes as $class => $packageName) {
            $annotations = $this->annotationService->getClassAnnotations($class);
            $group = $packageName;
            $name = $this->getShortName($class);
            if ($annotations->has('group')) {
                $group = (string) $annotations->get('group');
            }
            #if ($annotations->get("label"))
            #    $name = strval(current($annotations->get("label")));
            $groups[$group][] = array('being' => $class,
                'name' => $name
            );
        }
        return $groups;
    }

    /**
    * TODO: Document this Method! ( getShortName )
    */
    public function getShortName($class) {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $parts = explode('\\', $class);
        return array_pop($parts);
    }

}

?>