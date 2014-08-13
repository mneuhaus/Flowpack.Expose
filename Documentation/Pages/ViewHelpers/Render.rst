Render
------


ViewHelper that renders a section or a specified partial

== Examples ==

<code title="Rendering partials">
<f:render partial="SomePartial" arguments="{foo: someVariable}" />
</code>
<output>
the content of the partial "SomePartial". The content of the variable {someVariable} will be available in the partial as {foo}
</output>

<code title="Rendering sections">
<f:section name="someSection">This is a section. {foo}</f:section>
<f:render section="someSection" arguments="{foo: someVariable}" />
</code>
<output>
the content of the section "someSection". The content of the variable {someVariable} will be available in the partial as {foo}
</output>

<code title="Rendering recursive sections">
<f:section name="mySection">
 <ul>
   <f:for each="{myMenu}" as="menuItem">
     <li>
       {menuItem.text}
       <f:if condition="{menuItem.subItems}">
         <f:render section="mySection" arguments="{myMenu: menuItem.subItems}" />
       </f:if>
     </li>
   </f:for>
 </ul>
</f:section>
<f:render section="mySection" arguments="{myMenu: menu}" />
</code>
<output>
<ul>
  <li>menu1
    <ul>
      <li>menu1a</li>
      <li>menu1b</li>
    </ul>
  </li>
[...]
(depending on the value of {menu})
</output>


<code title="Passing all variables to a partial">
<f:render partial="somePartial" arguments="{_all}" />
</code>
<output>
the content of the partial "somePartial".
Using the reserved keyword "_all", all available variables will be passed along to the partial
</output>



Arguments
=========

=========  =======  ========  ===================================================================================================================================================================
Name       Type     Required  Description                                                                                                                                                          
=========  =======  ========  ===================================================================================================================================================================
section    string   no        Name of section to render. If used in a layout, renders a section of the main content file. If used inside a standard template, renders a section of the same file.  
partial    string   no        Reference to a partial.                                                                                                                                              
arguments  array    no        Arguments to pass to the partial.                                                                                                                                    
optional   boolean  no        Set to TRUE, to ignore unknown sections, so the definition of a section inside a template can be optional for a layout                                               
=========  =======  ========  ===================================================================================================================================================================

