# ContentManagement Module for TYPO3.TYPO3 using TYPO3.Form

## TODO list

* Refactor Reflection API
* Naming: change package namespace to "TYPO3.Admin"
* Naming: change "Feature" to "Admin Controller"

## Installation

```
  git clone --recursive git://git.typo3.org/TYPO3v5/Distributions/Base.git PhoenixContentManagement
  cd PhoenixContentManagement

  git clone git@github.com:mneuhaus/Demo.ContentManagement.git Packages/Application/Demo.ContentManagement
  git clone git@github.com:mneuhaus/Foo.ContentManagement.git Packages/Application/Foo.ContentManagement
```

See corresponding forge issue: http://forge.typo3.org/issues/37293


### Apply the following changesets:

```
	TYPO3.TYPO3:

	# https://review.typo3.org/#/c/13491/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TYPO3 refs/changes/91/13491/4 && git cherry-pick FETCH_HEAD


	TYPO3.TypoScript:

	# https://review.typo3.org/#/c/12648/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TypoScript refs/changes/48/12648/1 && git cherry-pick FETCH_HEAD

	# https://review.typo3.org/#/c/12655/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TypoScript refs/changes/55/12655/1 && git cherry-pick FETCH_HEAD

	# https://review.typo3.org/#/c/12649/
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TypoScript refs/changes/49/12649/3 && git cherry-pick FETCH_HEAD

	# http://review.typo3.org/13500
	git fetch git://git.typo3.org/FLOW3/Packages/TYPO3.TypoScript refs/changes/00/13500/1 && git cherry-pick FETCH_HEAD


	TYPO3.Party:

	# apply the patch from Patches/AbstractParty.diff


	TYPO3.FLOW3:

	# apply the patch from Patches/PropertyMapper.diff
	# apply the patch from Patches/PropertyMappingConfiguration.diff

```