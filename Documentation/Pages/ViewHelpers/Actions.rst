Actions
-------


This viewhelper looks for actions annotated with the ``\Flowpack\Expose\Annotations\Action`` annotation and filter them
by the type of action specified (local, global, batch)

Example
=======

.. code-block:: html

  <e:actions type="global">
    <f:for each="{actions}" key="action" as="actionAnnotation">
      <e:link.action action="{action}" class="{actionAnnotation.class}">
        {actionAnnotation.label}
      </e:link.action>
    </f:for>
  </e:actions>



Arguments
=========

====  ======  ========  =================================================
Name  Type    Required  Description                                        
====  ======  ========  =================================================
type  string  yes       Type of actions to return [local|global|batch]     
as    string  no        Variable to assign the actions into the view with  
====  ======  ========  =================================================

