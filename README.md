# Admin Module for TYPO3.TYPO3 using TYPO3.Form

## TODO list

* Refactor Reflection API
* Naming: change package namespace to "TYPO3.Admin"
* Naming: change "Feature" to "Admin Controller"

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

	TYPO3.Party:

	# apply the patch from Patches/AbstractParty.diff


	TYPO3.FLOW3:

	# apply the patch from Patches/PropertyMappingConfiguration.diff

```