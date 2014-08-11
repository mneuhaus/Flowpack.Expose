============
Installation
============

Composer
========

add this repository to your composer.json:

.. code-block:: json

  {
    "type": "vcs",
    "url": "git@github.com:mneuhaus/Flowpack.Expose.git"
  }


.. code-block:: none

  composer require flowpack/expose


Required pending ChangeSets
===========================

create an ``beard.json`` file with this content in your project root:

.. code-block:: json

  {
    "defaults": {
      "gerrit_api_endpoint": "https://review.typo3.org/",
      "gerrit_git": "git.typo3.org"
    },
    "changes": [
      {
        "name": "[WIP][FEATURE] Add a way to clear caches by Path + FilePattern",
        "type": "gerrit",
        "path": "Packages/Framework/TYPO3.Flow",
        "change_id": "25078"
      },
      {
        "name": "!!![WIP][FEATURE] ControllerInheritence Fallbacks",
        "type": "gerrit",
        "path": "Packages/Framework/TYPO3.Fluid",
        "change_id": "31939"
      }
    ]
  }

Then run the following command in your root directory:

.. code-block:: none

  beard patch


``beard`` is a little helper to automatically patch based on gerrit
changes specified in gerrit.json. (https://github.com/mneuhaus/Beard)