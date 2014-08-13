Property
--------


You can use this viewHelper to retrieve a property from ab object based on the name of the property stored in a variabl

Example
=======

.. code-block:: html

  <f:for each="{properties}" as="property">
    <e:property object="{object}" name="{property}" />
  </f:for>



Arguments
=========

======  ======  ========  ===============================================
Name    Type    Required  Description                                      
======  ======  ========  ===============================================
object  object  yes       Object to get the property or propertyPath from  
name    string  yes       Name of the property or propertyPath             
======  ======  ========  ===============================================

