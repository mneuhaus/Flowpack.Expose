# ContentManagement Module for TYPO3.TYPO3 using TYPO3.Form

## TODO list

* Get rid of Action abstraction, and rather use Controller API directly.
* See code comments (grep for: "TODO: (SK)")

## Installation

```
  git clone --recursive git://git.typo3.org/TYPO3v5/Distributions/Base.git PhoenixContentManagement
  cd PhoenixContentManagement

  git clone git@github.com:mneuhaus/Demo.ContentManagement.git Packages/Application/Demo.ContentManagement
  git clone git@github.com:mneuhaus/Foo.ContentManagement.git Packages/Application/Foo.ContentManagement
```

Apply patches from 'Packages/Application/Foo.ContentManagement/Patches' and 'https://review.typo3.org/#/q/status:open+project:FLOW3/Packages/TYPO3.TypoScript+branch:master+topic:typoscript_38692,n,z'

See corresponding forge issue: http://forge.typo3.org/issues/37293
