# ContentManagement Module for TYPO3.TYPO3 using TYPO3.Form

## TODO list

* Get rid of Action abstraction, and rather use Controller API directly.
* See code comments (grep for: "TODO: (SK)")
* Get rid of assetic for now.

## Installation

```
  git clone --recursive git://git.typo3.org/TYPO3v5/Distributions/Base.git PhoenixContentManagement
  cd PhoenixContentManagement

  git clone https://github.com/afoeder/Assetic-Package Packages/Application/Assetic
  git clone https://github.com/afoeder/Symfony.Component.Process.git Packages/Application/Symfony.Component.Process
  git clone https://github.com/afoeder/TYPO3.Asset.git Packages/Application/TYPO3.Asset
  git clone https://github.com/mneuhaus/LessPHP-Package Packages/Application/LessPHP

  // Replace Twitter.Bootstrap with modified version:
  cd Packages/Application/
  rm -rf Twitter.Bootstrap
  git clone git@github.com:mneuhaus/Twitter.Bootstrap.git
  cd Twitter.Bootstrap
  git checkout asset_integration
  cd ../../../

  git clone git@github.com:mneuhaus/Demo.ContentManagement.git Packages/Application/Demo.ContentManagement
  git clone git@github.com:mneuhaus/Foo.ContentManagement.git Packages/Application/Foo.ContentManagement
```

Apply patches from 'Packages/Application/Foo.ContentManagement/Patches' and 'https://review.typo3.org/#/q/status:open+project:FLOW3/Packages/TYPO3.TypoScript+branch:master+topic:typoscript_38692,n,z'

See corresponding forge issue: http://forge.typo3.org/issues/37293
