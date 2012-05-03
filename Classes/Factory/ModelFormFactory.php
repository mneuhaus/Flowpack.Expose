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
        $form = new FormDefinition('moduleArguments', $formConfiguration);
        
        $object = $factorySpecificConfiguration["object"];

        $being = new \Foo\ContentManagement\Core\Being($this->contentManager->getAdapterByClass(get_class($object)));
        $being->setClass(get_class($object));
        $being->setObject($object);

        $classAnnotations = $this->contentManager->getClassAnnotations(get_class($object));

        $page1 = $form->createPage('page1');

        $elements = array();

        if(!$this->contentManager->isNewObject($object)){
            $elements["__identity"] = $page1->createElement("item.__identity", "Foo.ContentManagement:Hidden");
            $elements["__identity"]->setDefaultValue($this->contentManager->getId($object));
        }

        $form->getProcessingRule("item")->setDataType(get_class($object));
        $form->getProcessingRule("item")->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
        $form->getProcessingRule("item")->getPropertyMappingConfiguration()->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);

        foreach ($being->getSets() as $set => $properties) {
            foreach ($properties as $name => $property) {
                $propertyAnnotations = $classAnnotations->getPropertyAnnotations($name);

                $elements[$name] = $page1->createElement("item.".$name, $property->getWidget());
                $elements[$name]->setLabel($property->label);
                $elements[$name]->setDefaultValue($property->getValue());
                $elements[$name]->setProperty("annotations", $propertyAnnotations);
            }
        }
        $actionFinisher = new \Foo\ContentManagement\Finishers\ActionFinisher();
        $actionFinisher->setOption('class', $being->class);
        $form->addFinisher($actionFinisher);
        

        $form->createFinisher("TYPO3.Form:Redirect", array(
            'action' => 'list',
            "arguments" => array(
                "being" => $being->class
            )
        ));

        return $form;
    }
}

?>