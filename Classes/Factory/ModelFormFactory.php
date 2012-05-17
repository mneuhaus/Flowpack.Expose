<?php

namespace Foo\ContentManagement\Factory;

use TYPO3\FLOW3\Annotations as FLOW3;
use TYPO3\Form\Core\Model\FormDefinition;

class ModelFormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {
    /**
     * @var \Foo\ContentManagement\Adapters\ContentManager
     * @FLOW3\Inject
     */
    protected $contentManager;


    /**
     * @param array $factorySpecificConfiguration
     * @param string $presetName
     * @return \TYPO3\Form\Core\Model\FormDefinition
     */
    public function build(array $factorySpecificConfiguration, $presetName) {
        $formConfiguration = $this->getPresetConfiguration($presetName);
        $this->form = new FormDefinition('moduleArguments', $formConfiguration);
        
        $object = $factorySpecificConfiguration["object"];

        $page = $this->form->createPage('page');

        $elements = $this->generateElements($object, $page, "item");

        $actionFinisher = new \Foo\ContentManagement\Finishers\ActionFinisher();
        $actionFinisher->setOption('class', get_class($object));
        $this->form->addFinisher($actionFinisher);
        

        $this->form->createFinisher("TYPO3.Form:Redirect", array(
            'action' => 'list',
            "arguments" => array(
                "being" => get_class($object)
            )
        ));

        return $this->form;
    }

    public function generateElements($object, $page, $namespace = ""){
        $classAnnotations = $this->contentManager->getClassAnnotations(get_class($object));

        $elements = array();

        if(!$this->contentManager->isNewObject($object)){
            $elements["__identity"] = $page->createElement($namespace . ".__identity", "Foo.ContentManagement:Hidden");
            $elements["__identity"]->setDefaultValue($this->contentManager->getId($object));
        }

        $this->form->getProcessingRule($namespace)->setDataType(get_class($object));
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
        $this->form->getProcessingRule($namespace)->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);

        foreach ($classAnnotations->getSets() as $set => $properties) {
            foreach ($properties as $name => $property) {
                $propertyAnnotations = $classAnnotations->getPropertyAnnotations($name);

                $elements[$name] = $page->createElement($namespace . "." . $name, $property->getWidget());
                $elements[$name]->setLabel($property->getLabel());
                $elements[$name]->setDefaultValue($property->getValue());
                $elements[$name]->setProperty("annotations", $propertyAnnotations);

#                var_dump($propertyAnnotations->get("inline"));
#                if($propertyAnnotations->has("inline")){
#                }

                foreach ($propertyAnnotations as $annotation) {
                    if(method_exists($annotation, "modifyFormElement")){
                        $elements[$name] = $annotation->modifyFormElement($elements[$name], $page);
                    }
                }
            }
        }
        return $elements;
    }
}

?>