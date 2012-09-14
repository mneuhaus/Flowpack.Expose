<?php
namespace TYPO3\Expose\Reflection\Provider;

/* *
 * This script belongs to the TYPO3.Expose package.              *
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
 * Configurationprovider for the DummyAdapter
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DefaultsAnnotationProvider extends AbstractAnnotationProvider {

    /**
     * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
     * @FLOW3\Inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
    * TODO: Document this Method! ( getClassAnnotations )
    */
    public function getClassAnnotations($class) {
        $defaults = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.Defaults');
        $annotations = array();
        foreach ($defaults as $annotationName => $values) {
            if ($annotationName == 'Properties') {
                $propertyDefaults = $values;
                $properties = array_flip($this->reflectionService->getClassPropertyNames($class));
                #$schema = $this->reflectionService->getClassSchema($class);
                #if(is_object($schema)){
                #	$properties = $schema->getProperties();
                #}
                $annotations['Properties'] = array();
                foreach ($properties as $property => $meta) {
                    if ($property == 'FLOW3_Persistence_Identifier') {
                        continue;
                    }
                    $propertyAnnotations = array();
                    foreach ($propertyDefaults as $annotationName => $values) {
                        $annotationClass = $this->findAnnotationByName($annotationName);
                        $values = $this->convert($values);
                        $annotation = new $annotationClass($values);
                        $this->addAnnotation($propertyAnnotations, $annotation);
                    }
                    $annotations['Properties'][$property] = $propertyAnnotations;
                }
            } else {
                $annotationClass = $this->findAnnotationByName($annotationName);
                $values = $this->convert($values);
                $annotation = new $annotationClass($values);
                $this->addAnnotation($annotations, $annotation);
            }
        }
        return $annotations;
    }

    /**
    * TODO: Document this Method! ( convert )
    */
    public function convert($input) {
        if (is_array($input)) {
            return $input;
        } else {
            return array('value' => $input
            );
        }
    }

}

?>