=============
Configuration
=============

By default Expose will parse the Class you're working with by itself and use defaults to give
you the best default experience it can. You can additionally create a Configuration File in your
Configuration Folder called ``Expose.ModelName.yaml``.

The General Syntax of this File looks like this

.. code-block:: yaml

  '\My\Package\Domain\Model\MyModel':
      listProperties: someProperty
      defaultSortBy: someProperty
      properties:
          someProperty:
              label: Some cool Property
              control: Textarea


Class Configurations
====================

=================  =======================================================================
Name               Description
=================  =======================================================================
listProperties     Contains properties that should be display as columns in the list view
searchProperties   List of properties used by the SearchBehavior
filterProperties   List of properties used by the FilterBehavior
defaultSortBy      Default property to sort by
defaultOrder       Default order to sort the property
defaultWrap        Default wrap to use for the form controls
layout             Layout used by the Crud Controller
listBehaviors      Array of Behaviors that are used by the list view.
=================  =======================================================================

Property Configurations
=======================

===============  =======================================================================
Name             Description
===============  =======================================================================
label            Description that will be places under the form control
control          Contains properties that should be display as columns in the list view
infotext         Description that will be places under the form control
optionsProvider  Description that will be places under the form control
wrap             Description that will be places under the form control
===============  =======================================================================