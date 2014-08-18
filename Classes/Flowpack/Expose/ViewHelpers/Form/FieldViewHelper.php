<?php
namespace Flowpack\Expose\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use Flowpack\Expose\Reflection\ClassSchema;
use Flowpack\Expose\Reflection\PropertySchema;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * You can use this ViewHelper to create complete form fields for your form.
 * By default you have to take care of quite a lot of things yourself to render a form field, like
 *
 * * form control
 * * label
 * * relation between label and form control to enable focusing by clicking on the label
 * * wrapper around the label + control for better styling
 * * showing validation errors next to the form control
 * * add a class to the wrapper around the label + control to indicate an validation error
 * * maybe add an infotext
 *
 * To make this easier and reduce the fluid code needed you can use this viewhelper like this:
 *
 * Basic usage
 * ===========
 *
 * .. code-block:: xml
 *
 *   <e:form.field name="foo" control="Textfield" wrap="Default" value="bar" />
 *
 * This will render a ``Textfield`` with the name ``foo`` inside the default wrapper based on Bootstrap 3
 * and a value of ``bar`
 *
 * .. code-block:: html
 *
 *   <div class="form-group">
 *     <label for="foo" class="col-sm-3 control-label">Foo</label>
 *     <div class="col-sm-9">
 *       <input class="form-control" id="foo" type="text" name="foo" value="bar">
 *     </div>
 *   </div>
 *
 * **Output of the same field when validation failed**
 *
 * .. code-block:: html
 *
 *   <div class="form-group has-error">
 *     <label for="foo" class="col-sm-3 control-label">Foo</label>
 *     <div class="col-sm-9">
 *       <input class="form-control" id="foo" type="text" name="foo" value="bar">
 *       <span class="help-block">This property is required.</span>
 *     </div>
 *   </div>
 *
 * Usage with an object bound form
 * ===============================
 *
 * To make things even easier you can use it in combinatin with the binding of objects to you form like this:
 *
 * .. code-block:: xml
 *
 *   <f:form action="create" object="myObject" name="myObject">
 *     <e:form.field property="someString" />
 *     <e:form.field property="someRelation" />
 *     <e:form.field property="someBoolean" />
 *     ...
 *   </f:form>
 *
 * This will automatically resolve the control that should be used based on the property type and use the default wrap.
 *
 * .. code-block:: html
 *
 *	 <form action="...">
 *     <div class="form-group">
 *       <label for="someString" class="col-sm-3 control-label">Some String</label>
 *       <div class="col-sm-9">
 *         <input class="form-control" id="someString" type="text" name="someString">
 *       </div>
 *     </div>
 *     <div class="form-group">
 *       <label for="someRelation" class="col-sm-3 control-label">Some String</label>
 *       <div class="col-sm-9">
 *         <input class="form-control" id="someRelation" type="text" name="someRelation">
 *         <select class="form-control" id="someRelation" name="someRelation">
 *           <!-- Options provided by the RelationOptionsProvider -->
 *         </select>
 *       </div>
 *     </div>
 *     <div class="form-group">
 *       <label for="someString" class="col-sm-3 control-label">Some String</label>
 *       <div class="col-sm-9">
 *         <input class="form-control" id="someString" type="text" name="someString">
 *       </div>
 *     </div>
 *   </form>
 */
class FieldViewHelper extends AbstractFormFieldViewHelper {

	/**
	 * @var array
	 * @Flow\Inject(setting="FieldTypes")
	 */
	protected $fieldTypes;

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\Kickstart\Utility\Inflector
	 * @Flow\Inject
	 */
	protected $inflector;

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('control', 'string', 'Specifies the control to use to render this field', FALSE, NULL);
		$this->registerArgument('wrap', 'string', 'Specifies the wrap used to render the field', FALSE, 'Default');
		$this->registerUniversalTagAttributes();
	}

	/**
	 *
	 * @return string Rendered string
	 * @api
	 */
	public function render() {
		if (empty($this->arguments['class'])) {
			$this->arguments['class'] = 'form-control';
		}

		$property = $this->getProperty();
		$this->arguments['propertySchema'] = $property;
		$this->arguments['value'] = $this->getValue(FALSE);


		if ($this->arguments['control'] !== NULL) {
			$property->setControl($this->arguments['control']);
			$partial = $this->arguments['control'];
		} else {
			$partial = $property->getControl();
		}

		$control = $this->viewHelperVariableContainer->getView()->renderPartial('Form/Field/' . $partial, NULL, $this->arguments);
		if (empty($this->arguments['wrap'])) {
			return $control;
		}

		return $this->viewHelperVariableContainer->getView()->renderPartial('Form/Wrap/' . $this->arguments['wrap'], NULL, array_merge(
			$this->arguments,
			array('control' => $control)
		));
	}

	public function getProperty() {
		if (empty($this->arguments['property']) === FALSE) {
			$className = $this->templateVariableContainer->get('className');
			$classSchema = new ClassSchema($className);
			if ($this->viewHelperVariableContainer->exists('TYPO3\Fluid\ViewHelpers\FormViewHelper', 'formObject')) {
				$formObject = $this->viewHelperVariableContainer->get('TYPO3\Fluid\ViewHelpers\FormViewHelper', 'formObject');
				$classSchema->setObject($formObject);
			}
			$property = $classSchema->getProperty($this->arguments['property']);
		} else {
			$property = new PropertySchema(array(
				'control' => $this->resolveTypeByValue(),
				'label' => $this->inflector->humanizeCamelCase($this->arguments['name'], FALSE),
				'name' => $this->arguments['name'],
				'infotext' => ''
			));
		}
		$property->setFormName($this->getName());
		return $property;
	}

	public function resolveTypeByValue() {
		$value = $this->getValue(FALSE);

		if (is_string($value) || is_null($value)) {
			return 'string';
		} elseif (is_array($value)) {
			return 'array';
		} elseif (is_float($value)) {
			return 'float';
		} elseif (is_integer($value)) {
			return 'integer';
		} elseif (is_bool($value)) {
			return 'boolean';
		} elseif ($value instanceof \Traversable) {
			return 'MultiSelect';
		} elseif (is_object($value)) {
			$class = get_class($value);
			$parentClasses = class_parents($class);
			$interfaces = class_implements($class);
			return array_merge(array($class), $parentClasses, $interfaces, array('object'));
		} else {
			#throw new \Exception('The source is not of type string, array, float, integer, boolean or object, but of type "' . gettypeByValue($source) . '"', 1297773150);
		}
	}
}

?>