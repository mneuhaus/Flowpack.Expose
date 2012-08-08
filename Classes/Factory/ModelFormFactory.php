<?php
namespace TYPO3\Admin\Factory;

/*
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
use TYPO3\Form\Core\Model\FormDefinition;

/**
 * Fatory to create Forms based on Entities
 *
 **/
class ModelFormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {

    /**
     * @var \TYPO3\Admin\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

    /**
     * @var \TYPO3\Admin\Core\MetaPersistenceManager
     * @FLOW3\Inject
     */
    protected $metaPersistenceManager;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * @var \TYPO3\FLOW3\Validation\ValidatorResolver
     * @FLOW3\Inject
     */
    protected $validatorResolver;

    /**
     * @param array $factorySpecificConfiguration
     * @param string $presetName
     * @return \TYPO3\Form\Core\Model\FormDefinition
     */
    public function build(array $factorySpecificConfiguration, $presetName) {
        $formConfiguration = $this->getPresetConfiguration($presetName);
        $this->form = new FormDefinition('contentForm', $formConfiguration);
        if (isset($factorySpecificConfiguration['class'])) {
            $object = $this->metaPersistenceManager->getObject($factorySpecificConfiguration['class']);
        }
        if (isset($factorySpecificConfiguration['object'])) {
            $object = $factorySpecificConfiguration['object'];
        }
        $page = $this->form->createPage('page');
        $elements = $this->generateElements($object, $page, 'item');
        $actionFinisher = new \TYPO3\Admin\Finishers\ControllerCallbackFinisher();
        $actionFinisher->setOption('class', get_class($object));
        $actionFinisher->setOption('controllerCallback', $factorySpecificConfiguration['controllerCallback']);
        $this->form->addFinisher($actionFinisher);
        return $this->form;
    }

    /**
    * TODO: Document this Method! ( generateElements )
    */
    public function generateElements($object, $section, $namespace = '') {
        $class = $this->reflectionService->getClassNameByObject($object);
        $classAnnotations = $this->annotationService->getClassAnnotations($class);
        $classAnnotations->setObject($object);
        $elements = array();
        $this->form->getProcessingRule($namespace)->setDataType($class);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\\FLOW3\\Property\\TypeConverter\\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\\FLOW3\\Property\\TypeConverter\\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->allowAllPropertiesRecursivly();
        foreach ($classAnnotations->getSets() as $set => $properties) {
            foreach ($properties as $name => $property) {
                $propertyAnnotations = $classAnnotations->getPropertyAnnotations($name);
                if ($propertyAnnotations->has('ignore') && $propertyAnnotations->get('ignore')->ignoreContext('form')) {
                    continue;
                }
                if ($propertyAnnotations->has('inject')) {
                    continue;
                }
                $namespacedName = ($namespace . '.') . $name;
                $this->form->getProcessingRule($namespacedName)->getPropertyMappingConfiguration()->allowAllPropertiesRecursivly();
                if ($propertyAnnotations->has('inline')) {
                    $inlineVariant = $propertyAnnotations->getInline()->getVariant();
                    $type = $propertyAnnotations->getType();
                    $inlineAnnotations = $this->annotationService->getClassAnnotations($type);
                    // Create a Container for the "Rows" outside the later processed namespace
                    $containerSection = $section->createElement('container.' . $namespacedName, $inlineVariant);
                    $containerSection->setLabel($property->getLabel());
                    $containerSection->setAnnotations($inlineAnnotations);
                    $containerSection->setNamespace($namespacedName);
                    if ($propertyAnnotations->has('ManyToMany') || $propertyAnnotations->has('OneToMany')) {
                        // Check if the request contains the current namespace
                        // TODO
                        $arguments = array();
                        // $this->request->getArguments();
                        $objects = \TYPO3\FLOW3\Reflection\ObjectAccess::getPropertyPath($arguments, $namespacedName);
                        if (is_array($objects)) {
                            foreach ($objects as $key => $value) {
                                $objects[$key] = new $type();
                            }
                        }
                        // Check for already existing saved values
                        if (count($objects) == 0) {
                            $objects = $property->getValue();
                        }
                        // Create a new Dummy
                        if (count($objects) == 0) {
                            $objects = array(new $type()
                            );
                        }
                        foreach ($objects as $key => $object) {
                            $itemSection = $containerSection->createElement(($namespacedName . '.') . $key, $inlineVariant . 'Item');
                            $itemSection->setLabel($property->getLabel());
                            $elements = array_merge($elements, $this->generateElements($object, $itemSection, ($namespacedName . '.') . $key));
                            $this->form->getProcessingRule($namespacedName)->getPropertyMappingConfiguration()->allowProperties($key);
                        }
                        $containerSection->setMultipleMode(true);
                    } else {
                        $object = $property->getValue();
                        if (is_null($object)) {
                            $object = new $type();
                        }
                        $itemSection = $containerSection->createElement($namespacedName, $inlineVariant . 'Item');
                        $itemSection->setLabel($property->getLabel());
                        $elements = array_merge($elements, $this->generateElements($object, $itemSection, $namespacedName));
                    }
                } else {
                    $elements[($namespace . '.') . $name] = $section->createElement(($namespace . '.') . $name, $property->getElement());
                    $elements[($namespace . '.') . $name]->setLabel($property->getLabel());
                    $elements[($namespace . '.') . $name]->setDefaultValue($property->getValue());
                    $elements[($namespace . '.') . $name]->setProperty('annotations', $propertyAnnotations);
                    if ($property->has('validate')) {
                        $validators = $property->getValidate();
                        foreach ($validators as $validator) {
                            $validator = $this->validatorResolver->createValidator($validator->type, $validator->options);
                            $elements[($namespace . '.') . $name]->addValidator($validator);
                        }
                    }
                }
                foreach ($propertyAnnotations as $annotation) {
                    if (method_exists($annotation, 'modifyFormElement')) {
                        $elements[$name] = $annotation->modifyFormElement($elements[$name], $section);
                    }
                }
            }
        }
        $object = $classAnnotations->getObject();
        if (!$this->metaPersistenceManager->isNewObject($object)) {
            $elements['__identity'] = $section->createElement($namespace . '.__identity', 'TYPO3.Admin:Hidden');
            $elements['__identity']->setDefaultValue($this->metaPersistenceManager->getIdentifierByObject($object));
        }
        return $elements;
    }

}

?>