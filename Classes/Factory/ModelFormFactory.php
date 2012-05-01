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

        $page1 = $form->createPage('page1');

        $elements = array();
        #$elements["being"] = $page1->createElement("__being", "TYPO3.Form:SingleLineText");
        #$elements["being"]->setDefaultValue($being->class);
        if(!is_null($being->id)){
            $elements["__identity"] = $page1->createElement("__identity", "Foo.ContentManagement:Hidden");
            $elements["__identity"]->setDefaultValue($being->id);
        }

        foreach ($being->getSets() as $set => $properties) {
            foreach ($properties as $name => $property) {
                $elements[$name] = $page1->createElement($name, $property->getWidget());
                $elements[$name]->setLabel($property->label);
                $elements[$name]->setDefaultValue($property->getValue());
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