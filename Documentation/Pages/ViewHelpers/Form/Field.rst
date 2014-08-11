Form/Field
----------


You can use this ViewHelper to create complete form fields for your form.
By default you have to take care of quite a lot of things yourself to render a form field, like

* form control
* label
* relation between label and form control to enable focusing by clicking on the label
* wrapper around the label + control for better styling
* showing validation errors next to the form control
* add a class to the wrapper around the label + control to indicate an validation error
* maybe add an infotext

To make this easier and reduce the fluid code needed you can use this viewhelper like this:

Basic usage
===========

.. code-block:: xml

  <e:form.field name="foo" control="Textfield" wrap="Default" value="bar" />

This will render a ``Textfield`` with the name ``foo`` inside the default wrapper based on Bootstrap 3
and a value of ``bar`

.. code-block:: html

  <div class="form-group">
    <label for="foo" class="col-sm-3 control-label">Foo</label>
    <div class="col-sm-9">
      <input class="form-control" id="foo" type="text" name="foo" value="bar">
    </div>
  </div>

**Output of the same field when validation failed**

.. code-block:: html

  <div class="form-group has-error">
    <label for="foo" class="col-sm-3 control-label">Foo</label>
    <div class="col-sm-9">
      <input class="form-control" id="foo" type="text" name="foo" value="bar">
      <span class="help-block">This property is required.</span>
    </div>
  </div>

Usage with an object bound form
===============================

To make things even easier you can use it in combinatin with the binding of objects to you form like this:

.. code-block:: xml

  <f:form action="create" object="myObject" name="myObject">
    <e:form.field property="someString" />
    <e:form.field property="someRelation" />
    <e:form.field property="someBoolean" />
    ...
  </f:form>

This will automatically resolve the control that should be used based on the property type and use the default wrap.

.. code-block:: html

 <form action="...">
    <div class="form-group">
      <label for="someString" class="col-sm-3 control-label">Some String</label>
      <div class="col-sm-9">
        <input class="form-control" id="someString" type="text" name="someString">
      </div>
    </div>
    <div class="form-group">
      <label for="someRelation" class="col-sm-3 control-label">Some String</label>
      <div class="col-sm-9">
        <input class="form-control" id="someRelation" type="text" name="someRelation">
        <select class="form-control" id="someRelation" name="someRelation">
          <!-- Options provided by the RelationOptionsProvider -->
        </select>
      </div>
    </div>
    <div class="form-group">
      <label for="someString" class="col-sm-3 control-label">Some String</label>
      <div class="col-sm-9">
        <input class="form-control" id="someString" type="text" name="someString">
      </div>
    </div>
  </form>



Arguments
=========

====================  =======  ========  ==========================================================================================================================
Name                  Type     Required  Description                                                                                                                 
====================  =======  ========  ==========================================================================================================================
additionalAttributes  array    no        Additional tag attributes. They will be added directly to the resulting HTML tag.                                           
data                  array    no        Additional data-* attributes. They will each be added with a "data-" prefix.                                                
name                  string   no        Name of input tag                                                                                                           
value                 mixed    no        Value of input tag                                                                                                          
property              string   no        Name of Object Property. If used in conjunction with <f:form object="...">, "name" and "value" properties will be ignored.  
control               string   no        Specifies the control to use to render this field                                                                           
wrap                  string   no        Specifies the wrap used to render the field                                                                                 
class                 string   no        CSS class(es) for this element                                                                                              
dir                   string   no        Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)                         
id                    string   no        Unique (in this file) identifier for this HTML element.                                                                     
lang                  string   no        Language for this element. Use short names specified in RFC 1766                                                            
style                 string   no        Individual CSS styles for this element                                                                                      
title                 string   no        Tooltip text of element                                                                                                     
accesskey             string   no        Keyboard shortcut to access this element                                                                                    
tabindex              integer  no        Specifies the tab order of this element                                                                                     
onclick               string   no        JavaScript evaluated for the onclick event                                                                                  
====================  =======  ========  ==========================================================================================================================

