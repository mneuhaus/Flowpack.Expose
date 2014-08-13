Form/PropertyHasResults
-----------------------


You can use this viewhelper to check if a property has validation errors.

Examples
=======

.. code-block:: html

  <div class="form-group {e:form.propertyHasResults(property: someProperty, then: 'has-error')}">
    ...
  </div>

.. code-block:: html

  <e:form.propertyHasResults property="someProperty">
    This property has some errors!
  </e:form.propertyHasResults>



Arguments
=========

========  ======  ========  ===================================================
Name      Type    Required  Description                                          
========  ======  ========  ===================================================
then      mixed   no        Value to be returned if the condition if met.        
else      mixed   no        Value to be returned if the condition if not met.    
property  string  yes       Name of the property to check for Validation errors  
========  ======  ========  ===================================================

