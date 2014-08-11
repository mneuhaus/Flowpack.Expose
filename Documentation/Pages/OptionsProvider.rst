===============
OptionsProvider
===============

What is an OptionsProvider
==========================
This OptionsProvider is very useful to add options to a select for a simple string property

OptionsProvider are used to provide Options for FormFields like
SingleSelectionMixin, MultiSelectionMixin and similar.
The interface consists of only 1 method called "getOptions" which should return
an associative array of key, value pairs.


Array
=====


This OptionsProvider is very useful to add options to a select for a simple string property

.. code-block:: yaml

  '\TYPO3\Party\Domain\Model\ElectronicAddress':
      properties:
         type:
             control: 'SingleSelect'
             optionsProvider:
                 Name: Array
                 Options:
                     new: 'New'
                     done: 'Done'
                     rejected: 'Rejected'



**Settings**

=======  ========  ==========================================
Name     Required  Description                                 
=======  ========  ==========================================
Options  yes       Contains the options that will be provided  
=======  ========  ==========================================




Constant
========


This OptionsProvider is used to load options from an Entities class
by using a regular expression to match existing constants

.. code-block:: yaml

     TYPO3\Party\Domain\Model\ElectronicAddress:
         Properties:
             type:
                 Element: TYPO3.Form:SingleSelectDropdown
                 OptionsProvider:
                     Name: ConstOptionsProvider
                     Regex: TYPE_.+



**Settings**

===========  ========  =======================================================================
Name         Required  Description                                                              
===========  ========  =======================================================================
Regex        yes       Contains a Regular Expression to filter the class constants              
EmptyOption  no        Set this setting to add an emtpy option to the beginning of the options  
===========  ========  =======================================================================




Country
=======


This OptionsProvider is provides a localized list of countries



**Settings**

===========  ========  =======================================================================
Name         Required  Description                                                              
===========  ========  =======================================================================
EmptyOption  no        Set this setting to add an emtpy option to the beginning of the options  
===========  ========  =======================================================================




Relation
========


This OptionsProvider is used to fetch entities based on the orm relation of a property.



**Settings**

===========  ========  =======================================================================
Name         Required  Description                                                              
===========  ========  =======================================================================
QueryMethod  no        Method to call on the Repository to create a query                       
EmptyOption  no        Set this setting to add an emtpy option to the beginning of the options  
===========  ========  =======================================================================




Role
====


OptionsProvider for Policy Roles






Custom OptionsProvider
======================

You can easily create an optionsProvider for your special usecase.

.. code-block:: php

    <?php
    class MyOptionsProvider extends \Flowpack\Expose\Core\OptionsProvider\AbstractOptionsProvider {
        /**
         * @return array $options
         */
        public function getOptions() {
            $myOptions = array();
            // some logic to actually fill $myOptions
            return $myOptions;
        }
    }
    ?>