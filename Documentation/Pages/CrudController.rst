==============
CrudController
==============

The ``CrudController`` enables you to quickly create a controller with
a complete CRUD workflow for your entities.
Given you have an entity called '\My\Package\Domain\Model\Company'.
Then you can simply create a ``CompanyController`` in your package
that extends the ``CrudController`` like this:

.. code-block:: php

    <?php
    namespace My\Package\Controller;

    use Flowpack\Expose\Controller\CrudController;
    use TYPO3\Flow\Annotations as Flow;

    /**
     */
    class CompanyController extends CrudController {
        /**
         * @var string
         */
        protected $entity = '\My\Package\Domain\Model\Company';
    }


That's it, now you can go to ``/my.package/company/index`` to test the controller. You should see an empty listview and a button in the top
right corner to create a new company.
You don't need to copy any templates, layouts or partials by default.
Expose contains sensible default templates for list, new, edit, etc.

Change a template
=================

If you want to change a template all you need to do is to copy the
template that you want to use over to your CompanyController.

For example, if you want to change the edit template you need to copy
the file ``Flowpack.Expose/Resources/Private/Templates/Crud/Edit.html``
to ``My.Package/Resources/Private/Templates/Company/Edit.html`` the view
will automatically use your edit template instead of the default expose template.


**Templates**

+------------------------------+-----------------------------------------------+
| Template                     | Description                                   |
+==============================+===============================================+
| Templates/Crud/Index.html    | This template contains the list view for      |
|                              | the specified entity                          |
+------------------------------+-----------------------------------------------+
| Templates/Crud/New.html      | These templates contain a simple form which   |
| Templates/Crud/Edit.html     | display a form field for each of the          |
|                              | properties in the specified model.            |
+------------------------------+-----------------------------------------------+
| Templates/Crud/Show.html     | You can use this template to create a page    |
|                              | displays details about a specific Entity.     |
+------------------------------+-----------------------------------------------+


**Partials**

+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Template                          | Description                                                                                          |
+===================================+======================================================================================================+
| Partials/Pagination.html          | This partial contains the limit and pagination that is display under the list view.                  |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/SortField.html           | This partial is used by the SortBehavior to wrap sortable properties with some useful markup         |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Search.html              | This partial is used to render the search form above the list view                                   |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Table/Actions.html       | This partial renders the local actions for each row in the list view                                 |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Table/Body.html          | This partial renders the rows itself in the list view                                                |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Table/Header.html        | This partial renders the header of the list view                                                     |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Table/Layout.html        | This partial defines the general layout of the list view                                             |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Form/Wrap/Default.html   | This partial is used to wrap the rendered form control with label, validation errors, infotext, etc. |
+-----------------------------------+------------------------------------------------------------------------------------------------------+
| Partials/Form/Field/*.html        | These partials are used to render the different controls                                             |
+-----------------------------------+------------------------------------------------------------------------------------------------------+

Change existing Actions
=======================

since you inherit from the CrudController you can of course override any existing action to suit your needs.
This might be useful to set some properties of the model before persisting it inside the createAction.

Add new Actions
===============

Aside from altering existing actions you can create as many new actions as you like. If you want to include your new action
at some location in the default expose templates you can use the ``Flowpack\Expose\Annotations\Action`` annotation.

.. code-block:: php

    <?php
    namespace My\Package\Controller;

    use Flowpack\Expose\Controller\CrudController;
    use TYPO3\Flow\Annotations as Flow;
    use Flowpack\Expose\Annotations as Expose;

    /**
     */
    class CompanyController extends CrudController {
        /**
         * @var string
         */
        protected $entity = '\My\Package\Domain\Model\Company';

        /**
         * @Expose\Action(type="local", label="My Custom Action")
         * @param '\My\Package\Domain\Model\Company' $entity
         * @return void
         */
        public function myCustomAction($entity) {
          // ...
        }
    }

The ``Flowpack\Expose\Annotations\Action`` annotation has 3 options that you can set:

==================== =========================================================================================================================
Option               Description
==================== =========================================================================================================================
**label**            Contains the label that will be used to render the action
**type**             Contains the type of the action. This can be either ``global``, ``local`` or ``batch``. See the section below for details
**class**            Contains a class that will be added to the action link tag for styling
==================== =========================================================================================================================


Action types
------------

There are 3 different types of actions that you can define to be used by Expose:

**global**::

A global action is an action that can act without a specific entity. Expose includes the action ``new`` as a global action
to create a new entity of the specified type. These will be displayed in the top right corner of the list view by default.

**local**::

A local action is an action that receives a specific entity as argument and does something with that. Expose includes the actions
``edit``, ``delete`` and ``show`` as local actions. These will be displayed by default inside each row of entities in the list view

**batch**::

A batch action is an action that receives an array of entities to perform actions on them. Expose includes the action ``deleteBatch``
by default. These will be display in a select box above the list view and will be executed for each marked row, when you click on
the execute button right beside the select box.

