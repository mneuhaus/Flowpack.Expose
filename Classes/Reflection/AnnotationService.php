<?php
namespace TYPO3\Expose\Reflection;

/*
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
use TYPO3\Expose\Annotations as CM;

/**
 * TODO: (SK) while this makes sense in general, we should see how to integrate that into the reflection or annotation packages in FLOW3.
 * 		 (MN) I agree absolutely!
 *
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @FLOW3\Scope("singleton")
 */
class AnnotationService {

    /**
     * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
     * @FLOW3\Inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\FLOW3\Package\PackageManagerInterface
     * @FLOW3\Inject
     */
    protected $packageManager;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @api
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * returns ClassAnnotations
     *
     * @param string $class
     * @return array $classAnnotations
     * @CM\Cache
     */
    public function getClassAnnotations($class) {
        $implementations = class_implements('\\' . ltrim($class, '\\'));
        if (in_array('Doctrine\\ORM\\Proxy\\Proxy', $implementations)) {
            $class = get_parent_class('\\' . ltrim($class, '\\'));
        }
        $annotations = array();
        $annotationProviders = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose.AnnotationProvider');
        foreach ($annotationProviders as $annotationProviderClass) {
            $annotationProvider = new $annotationProviderClass();
            $annotations = $this->merge($annotations, $annotationProvider->getClassAnnotations($class));
        }
        $annotations = new Wrapper\ClassAnnotationWrapper($annotations);
        $annotations->setClass($class);
        return $annotations;
    }

    /**
     * returns classes that are taged with all of the specified tags
     *
     * @param string $tags
     * @return array $classes
     * @CM\Cache
     */
    public function getClassesAnnotatedWith($tags) {
        $classes = array();
        $activePackages = $this->packageManager->getActivePackages();
        foreach ($activePackages as $packageName => $package) {
            if (substr($packageName, 0, 8) === 'Doctrine') {
                continue;
            }
            foreach ($package->getClassFiles() as $class => $file) {
                $annotations = $this->getClassAnnotations($class);
                $tagged = true;
                foreach ($tags as $tag) {
                    if (!$annotations->has($tag)) {
                        $tagged = false;
                    }
                }
                if ($tagged) {
                    $classes[$class] = $packageName;
                }
            }
        }
        return $classes;
    }

    /**
    * TODO: Document this Method! ( merge )
    */
    public function merge($target, $source) {
        foreach ($source as $key => $value) {
            if ($key == 'Properties') {
                if (!isset($target[$key])) {
                    $target[$key] = array();
                }
                $target[$key] = $this->merge($target[$key], $value);
            } else {
                if (is_object($value)) {
                    $target[$key] = $value;
                }
                if (is_array($value)) {
                    if (!isset($target[$key])) {
                        $target[$key] = array();
                    }
                    $target[$key] = array_merge($target[$key], $value);
                }
            }
        }
        return $target;
    }

}

?>