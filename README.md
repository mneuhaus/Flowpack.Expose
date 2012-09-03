# Admin Module for TYPO3.TYPO3 using TYPO3.Form

## Current Status

* Basic listing + editing of nodes and domain objects works
* To test editing of domain objects: Install "Blog" package, set up demo blogs using CLI

## TODO list

* CREATION of objects
* Refactor Reflection API to make annotations overridable
* Sorting, Filtering, Paging of List view
* AJAX functionality
* Mass edit?
* Edit relations (Selection, ...)
* Further clean up of un-used code, resources, Configuration

## Installation

```
  git clone --recursive git://git.typo3.org/TYPO3v5/Distributions/Base.git PhoenixContentManagement
  cd PhoenixContentManagement

  git clone git@github.com:mneuhaus/Demo.ContentManagement.git Packages/Application/Demo.ContentManagement
  git clone git@github.com:mneuhaus/TYPO3.Admin.git Packages/Application/TYPO3.Admin
```

See corresponding forge issue: http://forge.typo3.org/issues/37293


### Apply the following changesets:

```

	TYPO3.TYPO3:
	# http://review.typo3.org/14313
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TYPO3 refs/changes/13/14313/1 && git checkout FETCH_HEAD

	TYPO3.Fluid:
	# https://review.typo3.org/#/c/13972
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.Fluid refs/changes/72/13972/1 && git cherry-pick FETCH_HEAD

```