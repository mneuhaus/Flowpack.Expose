PropertyResults
---------------


Checks if the specified property has errors and adds them as a variable to the view.

Example
=======

.. code-block:: html

  <e:form.propertyResults property="someProperty">
    <f:for each="{errors}" as="error">
      <p class="help-block">{error.message}</p>
    </f:for>
  </e:form.propertyResults>



Arguments
=========

========  ======  ========  =====================================================
Name      Type    Required  Description                                            
========  ======  ========  =====================================================
property  string  yes       Name of the propert to check for Validation errors     
as        string  no        Name of the variable the errors will be assigned into  
========  ======  ========  =====================================================

