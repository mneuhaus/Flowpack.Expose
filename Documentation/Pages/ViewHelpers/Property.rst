Property
--------


You can use this viewHelper to retrieve a property´s value from ab object based on the name or the schema of the property.
Properties with an Array OptionProvider defined, will be mapped to the option label with the current value used as index

Properties with a Relation OptionProvider defined, will show up with the value of the property defined on the LabelPath
(the last feature is only supported when you pass the propertyschema instead of the properties name)

Example
=======

.. code-block:: html

  <f:for each="{properties}" as="property">
    <e:property object="{object}" property="{property}" />
  </f:for>



Arguments
=========

========  ======  ========  ===============================================
Name      Type    Required  Description
========  ======  ========  ===============================================
object    object  yes       Object to get the property or propertyPath from
name      string  yes       Name of the property or propertyPath
property  object  yes       The property´s schema
========  ======  ========  ===============================================

