Filter
------




Arguments
=========

====================================  =======  ========  ================================================================================================================
Name                                  Type     Required  Description                                                                                                       
====================================  =======  ========  ================================================================================================================
additionalAttributes                  array    no        Additional tag attributes. They will be added directly to the resulting HTML tag.                                 
data                                  array    no        Additional data-* attributes. They will each be added with a "data-" prefix.                                      
property                              string   yes       Property to sort by                                                                                               
value                                 string   no        value to sort by                                                                                                  
action                                string   no        Target action                                                                                                     
arguments                             array    no        Arguments                                                                                                         
controller                            string   no        Target controller. If NULL current controllerName is used                                                         
package                               string   no        Target package. if NULL current package is used                                                                   
subpackage                            string   no        Target subpackage. if NULL current subpackage is used                                                             
section                               string   no        The anchor to be added to the URI                                                                                 
format                                string   no        The requested format, e.g. ".html                                                                                 
additionalParams                      array    no        additional query parameters that won't be prefixed like $arguments (overrule $arguments)                          
addQueryString                        boolean  no        If set, the current query parameters will be kept in the URI                                                      
argumentsToBeExcludedFromQueryString  array    no        arguments to be removed from the URI. Only active if $addQueryString = TRUE                                       
useParentRequest                      boolean  no        If set, the parent Request will be used instead of the current one                                                
absolute                              boolean  no        By default this ViewHelper renders links with absolute URIs. If this is FALSE, a relative URI is created instead  
class                                 string   no        CSS class(es) for this element                                                                                    
dir                                   string   no        Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)               
id                                    string   no        Unique (in this file) identifier for this HTML element.                                                           
lang                                  string   no        Language for this element. Use short names specified in RFC 1766                                                  
style                                 string   no        Individual CSS styles for this element                                                                            
title                                 string   no        Tooltip text of element                                                                                           
accesskey                             string   no        Keyboard shortcut to access this element                                                                          
tabindex                              integer  no        Specifies the tab order of this element                                                                           
onclick                               string   no        JavaScript evaluated for the onclick event                                                                        
name                                  string   no        Specifies the name of an anchor                                                                                   
rel                                   string   no        Specifies the relationship between the current document and the linked document                                   
rev                                   string   no        Specifies the relationship between the linked document and the current document                                   
target                                string   no        Specifies where to open the linked document                                                                       
====================================  =======  ========  ================================================================================================================

