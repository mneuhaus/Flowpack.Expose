<?php
namespace TYPO3\Expose\Form;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * This class encapsulates a complete *Form Definition*, with all of its pages,
 * form elements, validation rules which apply and finishers which should be
 * executed when the form is completely filled in.
 *
 * **This class is not meant to be subclassed by developers.**
 *
 * It is *not modified* when the form executes.
 *
 * The Anatomy Of A Form
 * =====================
 *
 * A FormDefinition consists of multiple *Page* ({@link Page}) objects. When a
 * form is displayed to the user, only one *Page* is visible at any given time,
 * and there is a navigation to go back and forth between the pages.
 *
 * A *Page* consists of multiple *FormElements* ({@link FormElementInterface}, {@link AbstractFormElement}),
 * which represent the input fields, textareas, checkboxes shown inside the page.
 *
 * *FormDefinition*, *Page* and *FormElement* have *identifier* properties, which
 * must be unique for each given type (i.e. it is allowed that the FormDefinition and
 * a FormElement have the *same* identifier, but two FormElements are not allowed to
 * have the same identifier.
 *
 * Simple Example
 * --------------
 *
 * Generally, you can create a FormDefinition manually by just calling the API
 * methods on it, or you use a *Form Definition Factory* to build the form from
 * another representation format such as YAML.
 *
 * /---code php
 * $formDefinition = new FormDefinition('myForm');
 *
 * $page1 = new Page('page1');
 * $formDefinition->addPage($page);
 *
 * $element1 = new GenericFormElement('title', 'TYPO3.Form:Textfield'); # the second argument is the type of the form element
 * $page1->addElement($element1);
 * \---
 *
 * Creating a Form, Using Abstract Form Element Types
 * =====================================================
 *
 * While you can use the {@link FormDefinition::addPage} or {@link Page::addElement}
 * methods and create the Page and FormElement objects manually, it is often better
 * to use the corresponding create* methods ({@link FormDefinition::createPage}
 * and {@link Page::createElement}), as you pass them an abstract *Form Element Type*
 * such as *TYPO3.Form:Text* or *TYPO3.Form.Page*, and the system **automatically
 * resolves the implementation class name and sets default values**.
 *
 * So the simple example from above should be rewritten as follows:
 *
 * /---code php
 * $formDefaults = array(); // We'll talk about this later
 *
 * $formDefinition = new FormDefinition('myForm', $formDefaults);
 * $page1 = $formDefinition->createPage('page1');
 * $element1 = $page1->addElement('title', 'TYPO3.Form:Textfield');
 * \---
 *
 * Now, you might wonder how the system knows that the element *TYPO3.Form:Textfield*
 * is implemented using a GenericFormElement: **This is configured in the $formDefaults**.
 *
 * To make the example from above actually work, we need to add some sensible
 * values to *$formDefaults*:
 *
 * <pre>
 * $formDefaults = array(
 *   'formElementTypes' => array(
 *     'TYPO3.Form:Page' => array(
 *       'implementationClassName' => 'TYPO3\Form\Core\Model\Page'
 *     ),
 *     'TYPO3.Form:Textfield' => array(
 *       'implementationClassName' => 'TYPO3\Form\Core\Model\GenericFormElement'
 *     )
 *   )
 * )
 * </pre>
 *
 * For each abstract *Form Element Type* we add some configuration; in the above
 * case only the *implementation class name*. Still, it is possible to set defaults
 * for *all* configuration options of such an element, as the following example
 * shows:
 *
 * <pre>
 * $formDefaults = array(
 *   'formElementTypes' => array(
 *     'TYPO3.Form:Page' => array(
 *       'implementationClassName' => 'TYPO3\Form\Core\Model\Page',
 *       'label' => 'this is the label of the page if nothing is specified'
 *     ),
 *     'TYPO3.Form:Textfield' => array(
 *       'implementationClassName' => 'TYPO3\Form\Core\Model\GenericFormElement',
 *       'label' = >'Default Label',
 *       'defaultValue' => 'Default form element value',
 *       'properties' => array(
 *         'placeholder' => 'Text which is shown if element is empty'
 *       )
 *     )
 *   )
 * )
 * </pre>
 *
 * Introducing Supertypes
 * ----------------------
 *
 * Some form elements like the *Text* field and the *Date* field have a lot in common,
 * and only differ in a few different default values. In order to reduce the typing
 * overhead, it is possible to specify a list of **superTypes** which are used as a
 * basis:
 *
 * <pre>
 * $formDefaults = array(
 *   'formElementTypes' => array(
 *     'TYPO3.Form:Base' => array(
 *       'implementationClassName' => 'TYPO3\Form\Core\Model\GenericFormElement',
 *       'label' = >'Default Label'
 *     ),
 *     'TYPO3.Form:Textfield' => array(
 *       'superTypes' => array('TYPO3.Form:Base'),
 *       'defaultValue' => 'Default form element value',
 *       'properties' => array(
 *         'placeholder' => 'Text which is shown if element is empty'
 *       )
 *     )
 *   )
 * )
 * </pre>
 *
 * Here, we specified that the *Textfield* uses *TYPO3.Form:Base* as **supertype**,
 * which can reduce typing overhead a lot. It is also possible to use *multiple
 * supertypes*, which are then evaluated in the order in which they are specified.
 *
 * Supertypes are evaluated recursively.
 *
 * Thus, default values are merged in the following order, while later values
 * override prior ones:
 *
 * - configuration of 1st supertype
 * - configuration of 2nd supertype
 * - configuration of ... supertype
 * - configuration of the type itself
 *
 * Using Preconfigured $formDefaults
 * ---------------------------------
 *
 * Often, it is not really useful to manually create the $formDefaults array.
 *
 * Most of it comes pre-configured inside the *TYPO3.Form* package's **Settings.yaml**,
 * and the {@link \TYPO3\Form\Factory\AbstractFormFactory} contains helper methods
 * which return the ready-to-use *$formDefaults*. Please read the documentation
 * on {@link \TYPO3\Form\Factory\AbstractFormFactory} for some best-practice
 * usage examples.
 *
 * Property Mapping and Validation Rules
 * =====================================
 *
 * Besides Pages and FormElements, the FormDefinition can contain information
 * about the *format of the data* which is inputted into the form. This generally means:
 *
 * - expected Data Types
 * - Property Mapping Configuration to be used
 * - Validation Rules which should apply
 *
 * Background Info
 * ---------------
 * You might wonder why Data Types and Validation Rules are *not attached
 * to each FormElement itself*.
 *
 * If the form should create a *hierarchical output structure* such as a multi-
 * dimensional array or a PHP object, your expected data structure might look as follows:
 * <pre>
 * - person
 * -- firstName
 * -- lastName
 * -- address
 * --- street
 * --- city
 * </pre>
 *
 * Now, let's imagine you want to edit *person.address.street* and *person.address.city*,
 * but want to validate that the *combination* of *street* and *city* is valid
 * according to some address database.
 *
 * In this case, the form elements would be configured to fill *street* and *city*,
 * but the *validator* needs to be attached to the *compound object* *address*,
 * as both parts need to be validated together.
 *
 * Connecting FormElements to the output data structure
 * ====================================================
 *
 * The *identifier* of the *FormElement* is most important, as it determines
 * where in the output structure the value which is entered by the user is placed,
 * and thus also determines which validation rules need to apply.
 *
 * Using the above example, if you want to create a FormElement for the *street*,
 * you should use the identifier *person.address.street*.
 *
 * Rendering a FormDefinition
 * ==========================
 *
 * In order to trigger *rendering* on a FormDefinition,
 * the current {@link \TYPO3\Flow\Mvc\ActionRequest} needs to be bound to the FormDefinition,
 * resulting in a {@link \TYPO3\Form\Core\Runtime\FormRuntime} object which contains the *Runtime State* of the form
 * (such as the currently inserted values).
 *
 * /---code php
 * # $currentRequest and $currentResponse need to be available, f.e. inside a controller you would
 * # use $this->request and $this->response; inside a ViewHelper you would use $this->controllerContext->getRequest()
 * # and $this->controllerContext->getResponse()
 * $form = $formDefinition->bind($currentRequest, $currentResponse);
 *
 * # now, you can use the $form object to get information about the currently
 * # entered values into the form, etc.
 * \---
 *
 * Refer to the {@link \TYPO3\Form\Core\Runtime\FormRuntime} API doc for further information.
 */
class FormDefinition extends \TYPO3\Form\Core\Model\FormDefinition {
	/**
	 * Collection of ignored identifiers used in the form
	 * layout to show validation errors for ignored
	 * properties
	 *
	 * @var array
	 */
	protected $ignoredIdentifiers = array();

	/**
	 * @param string $ignoredIdentifier
	 */
	public function addIgnoredIdentifier($ignoredIdentifier) {
		$this->ignoredIdentifiers[] = $ignoredIdentifier;
	}

	/**
	 * @param array $ignoredIdentifiers
	 */
	public function setIgnoredIdentifiers($ignoredIdentifiers) {
		$this->ignoredIdentifiers = $ignoredIdentifiers;
	}

	/**
	 * @return array
	 */
	public function getIgnoredIdentifiers() {
		return $this->ignoredIdentifiers;
	}

	/**
	 * @param string $propertyPath
	 * @return ProcessingRule
	 * @api
	 */
	public function getProcessingRule($propertyPath) {
		if (!isset($this->processingRules[$propertyPath])) {
			$this->processingRules[$propertyPath] = new ProcessingRule();
		}
		return $this->processingRules[$propertyPath];
	}
}
?>