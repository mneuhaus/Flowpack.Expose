# Expose Module for TYPO3.TYPO3 using TYPO3.Form

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
  Demo-Environment: 
```

See corresponding forge issue: http://forge.typo3.org/issues/37293


### Apply the following changesets:

```
	TYPO3.Flow:
	# https://review.typo3.org/#/c/16392/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.FLOW3 refs/changes/92/16392/3 && git cherry-pick FETCH_HEAD

	TYPO3.Expose:
	# https://review.typo3.org/#/c/16160/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.Expose refs/changes/60/16160/2 && git cherry-pick FETCH_HEAD

	TYPO3.Fluid:
	# https://review.typo3.org/#/c/16393/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.Fluid refs/changes/93/16393/3 && git cherry-pick FETCH_HEAD

	TYPO3.TypoScript:
	# https://review.typo3.org/#/c/16394/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TypoScript refs/changes/94/16394/3 && git cherry-pick FETCH_HEAD

```