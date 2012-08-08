<?php
namespace TYPO3\Admin\Core;

/* *
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
 * FeatureManager to retrieve and Initialize Actions
 *
 * TODO: (SK) the FeatureManager should be fully tested by unit tests or functional tests.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class FeatureManager {

    /**
     * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
     * @FLOW3\Inject
     */
    protected $objectManager;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * Find the features which should be linked at a certain $context of the application.
     *
     * @param string $context the context to find related features for, like "List" or "List.Element"
     * @param string $type the type of the objects currently being worked with
     * @return array an array of Feature objects being available for linking
     */
    public function findRelatedFeaturesByContext($context, $type = NULL) {
        $relatedFeatures = array();
        foreach ($this->reflectionService->getAllImplementationClassNamesForInterface('TYPO3\\Admin\\Core\\Features\\FeatureInterface') as $featureClassName) {
            var_dump($featureClassName);
            $feature = $this->objectManager->get($featureClassName);
            $sorting = $feature->isFeatureRelatedForContext($context, $type);
            if (is_integer($sorting) && $sorting > 0) {
                $relatedFeatures[$sorting] = $feature;
            }
        }
        ksort($relatedFeatures);
        return $relatedFeatures;
    }

}

?>