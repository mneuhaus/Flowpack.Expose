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

Flowpack.Expose OptionsProvider
===============================

RoleOptionsProvider
===================

ArrayOptionsProvider
====================

ConstOptionsProvider
===================

This OptionsProvider is used to load options from an Entities class by using a
regular expression to match existing constants.

.. code-block:: php

  <?php
  class ElectronicAddress {

    const TYPE_AIM = 'Aim';
    const TYPE_EMAIL = 'Email';
    const TYPE_ICQ = 'Icq';
    const TYPE_JABBER = 'Jabber';
    ...

    /**
     * @var string
     */
    protected $type;
  }

.. code-block:: yaml

  '\TYPO3\Party\Domain\Model\ElectronicAddress':
    properties:
      type:
        control: 'SingleSelect'
          optionsProvider:
            Name: Constant
            Regex: TYPE_.+


Custom OptionsProvider
======================

You can easily create an optionsProvider for your special usecase...