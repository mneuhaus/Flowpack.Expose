===============
OptionsProvider
===============

.. sectionauthor:: Marc Neuhaus <marc.neuhaus@typo3.org>

What is an OptionsProvider
==========================

OptionsProvider are used to provide Options for FormFields like
SingleSelectionMixin, MultiSelectionMixin and similar.
The interface consists of only 1 method called "getOptions" which should return
an associative array of key, value pairs.

TYPO3.Expose OptionsProvider
============================

RoleOptionsProvider
===================

ArrayOptionsProvider
====================

ConstOptionsProvider
===================

This OptionsProvider is used to load options from an Entities classby using a
regular expression to match existing constants

.. code-block:: typoscript

	prototype(TYPO3.Expose:Schema:TYPO3.Party.Domain.Model.ElectronicAddress) < prototype(TYPO3.Expose:Schema) {
		properties {
			cycle {
				element = 'TYPO3.Form:SingleSelectDropdown'
				optionsProvider {
					class = 'ConstOptionsProvider'
					RegEx = 'TYPE_.+'
				}
			}
		}
	}

Custom OptionsProvider
======================

You can easily create an optionsProvider for your special usecase...