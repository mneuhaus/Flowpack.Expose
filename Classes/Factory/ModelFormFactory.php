<?php

namespace Foo\ContentManagement\Factory;

use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\Form\Core\Model\FormDefinition;

class ModelFormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {

    /**
     * @var \Foo\ContentManagement\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

    /**
     * @var \Foo\ContentManagement\Core\MetaPersistenceManager
     * @FLOW3\Inject
     */
    protected $persistenceService;

    /**
     * @var \TYPO3\FLOW3\Mvc\ActionRequest
     * @internal
     */
    protected $request;

    /**
     * @var \TYPO3\FLOW3\Validation\ValidatorResolver
     * @FLOW3\Inject
     */
    protected $validatorResolver;
    

    public function setRequest(\TYPO3\FLOW3\Mvc\ActionRequest $request) {
        $this->request = $request;
    }

    /**
     * @param array $factorySpecificConfiguration
     * @param string $presetName
     * @return \TYPO3\Form\Core\Model\FormDefinition
     */
    public function build(array $factorySpecificConfiguration, $presetName) {
        $formConfiguration = $this->getPresetConfiguration($presetName);
        $this->form = new FormDefinition('contentForm', $formConfiguration);

        $this->setRequest($factorySpecificConfiguration["request"]);
        
        if(isset($factorySpecificConfiguration["class"]))
            $object = new $factorySpecificConfiguration["class"]();

        if(isset($factorySpecificConfiguration["object"]))
            $object = $factorySpecificConfiguration["object"];

        $page = $this->form->createPage('page');

        $elements = $this->generateElements($object, $page, "item");

        $actionFinisher = new \Foo\ContentManagement\Finishers\ControllerCallbackFinisher();
        $actionFinisher->setOption('class', get_class($object));
        $actionFinisher->setOption('controllerCallback', $factorySpecificConfiguration["controllerCallback"]);
        $this->form->addFinisher($actionFinisher);
        
        return $this->form;
    }

    public function generateElements($object, $section, $namespace = ""){
        $class = get_class($object);
        $classAnnotations = $this->annotationService->getClassAnnotations($class);
        $classAnnotations->setObject($object);

        $elements = array();

        $this->form->getProcessingRule($namespace)->setDataType($class);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->allowAllPropertiesRecursivly();

        foreach ($classAnnotations->getSets() as $set => $properties) {
            foreach ($properties as $name => $property) {
                $propertyAnnotations = $classAnnotations->getPropertyAnnotations($name);
                
                if($propertyAnnotations->has("ignore") && $propertyAnnotations->get("ignore")->ignoreContext("form")) continue;
                if($propertyAnnotations->has("inject")) continue;

                $namespacedName = $namespace . "." . $name;
                $this->form->getProcessingRule($namespacedName)->getPropertyMappingConfiguration()->allowAllPropertiesRecursivly();

                if($propertyAnnotations->has("inline")){

                    $inlineVariant = $propertyAnnotations->getInline()->getVariant();
                    $type = $propertyAnnotations->getType();
                    $inlineAnnotations = $this->annotationService->getClassAnnotations($type);
                    
                    // Create a Container for the "Rows" outside the later processed namespace
                    $containerSection = $section->createElement("container.".$namespacedName, $inlineVariant);
                    $containerSection->setLabel($property->getLabel());
                    $containerSection->setAnnotations($inlineAnnotations);
                    $containerSection->setNamespace($namespacedName);

                    if($propertyAnnotations->has("ManyToMany") || $propertyAnnotations->has("OneToMany")){
                        // Check if the request contains the current namespace
                        $arguments = $this->request->getArguments();
                        $objects = \TYPO3\FLOW3\Reflection\ObjectAccess::getPropertyPath($arguments, $namespacedName);
                        if(is_array($objects)){
                            foreach ($objects as $key => $value) {
                                $objects[$key] = new $type();
                            }
                        }

                        // Check for already existing saved values
                        if(count($objects) == 0)
                            $objects = $property->getValue();

                        // Create a new Dummy
                        if(count($objects) == 0){
                            $objects = array(new $type);
                        }

                        foreach ($objects as $key => $object) {
                            $itemSection = $containerSection->createElement($namespacedName . "." . $key, $inlineVariant.'Item');
                            $itemSection->setLabel($property->getLabel());
                            $elements = array_merge($elements, $this->generateElements($object, $itemSection, $namespacedName . "." . $key));
                            $this->form->getProcessingRule($namespacedName)->getPropertyMappingConfiguration()->allowProperties($key);
                        }
                        $containerSection->setMultipleMode(true);
                    }else{
                        $object = $property->getValue();
                        if(is_null($object))
                            $object = new $type();
                        $itemSection = $containerSection->createElement($namespacedName, $inlineVariant.'Item');
                        $itemSection->setLabel($property->getLabel());
                        $elements = array_merge($elements, $this->generateElements($object, $itemSection, $namespacedName));
                    }

                }else{

                    $elements[$namespace . "." . $name] = $section->createElement($namespace . "." . $name, $property->getElement());
                    $elements[$namespace . "." . $name]->setLabel($property->getLabel());
                    $elements[$namespace . "." . $name]->setDefaultValue($property->getValue());
                    $elements[$namespace . "." . $name]->setProperty("annotations", $propertyAnnotations);
                    if($property->has("validate")){
                        $validators = $property->getValidate();
                        foreach ($validators as $validator) {
                            $validator = $this->validatorResolver->createValidator($validator->type, $validator->options);
                            $elements[$namespace . "." . $name]->addValidator($validator);
                        }
                    }

                }

                foreach ($propertyAnnotations as $annotation) {
                    if(method_exists($annotation, "modifyFormElement")){
                        $elements[$name] = $annotation->modifyFormElement($elements[$name], $section);
                    }
                }
            }
        }

        $object = $classAnnotations->getObject();
        if(!$this->persistenceService->isNewObject($object)){
            $elements["__identity"] = $section->createElement($namespace . ".__identity", "Foo.ContentManagement:Hidden");
            $elements["__identity"]->setDefaultValue($this->persistenceService->getIdentifierByObject($object));
        }

        return $elements;
    }
}

?>