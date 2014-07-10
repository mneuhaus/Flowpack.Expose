<?php
namespace TYPO3\Expose\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
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
		$this->registerArgument('control', 'string', 'Specifies the type of the properties value', FALSE, NULL);
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
		$this->arguments['property'] = $property;

		if ($this->arguments['control'] !== NULL) {
			$partial = $this->arguments['control'];
		} else {
			$partial = $property['partial'];
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
			$classSchema = new \TYPO3\Expose\Schema\DefaultSchema($className);
			$property = $classSchema->getProperty($this->arguments['property']);
		} else {
			$property = array(
				'metaType' => $this->getTypeByValue(),
				'label' => $this->inflector->humanizeCamelCase($this->arguments['name'], FALSE)
			);
		}

		$property['name'] = $this->getName();
		$this->arguments['value'] = $this->getValue();

		if (isset($this->fieldTypes[$property['metaType']]) === TRUE) {
			$property['partial'] = $this->fieldTypes[$property['metaType']];
		} else {
			$property['partial'] = $property['metaType'];
		}
		return $property;
	}

	public function getTypeByValue() {
		$value = $this->getValue();

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