# ContentManagement Module for TYPO3.TYPO3 using TYPO3.Form

## Installation

 git clone --recursive git://git.typo3.org/TYPO3v5/Distributions/Base.git FLOW3
 cd FLOW3
 git clone https://github.com/afoeder/TYPO3.Asset.git Packages/Application/TYPO3.Asset
 git clone https://github.com/afoeder/Assetic-Package Packages/Application/Assetic
 git clone https://github.com/afoeder/Symfony.Component.Process.git Packages/Application/Assetic

 git clone https://github.com/afoeder/TYPO3.Asset.git Packages/Application/TYPO3.Asset

 // Replace Twitter.Bootstrap with modified version:
 cd Packages/Application/
 rm -rf Twitter.Bootstrap
 git clone git@github.com:mneuhaus/Twitter.Bootstrap.git
 git checkout asset_integration

 git clone git@github.com:mneuhaus/Demo.ContentManagement.git Packages/Application/Demo.ContentManagement
 git clone git@github.com:mneuhaus/Foo.ContentManagement.git Packages/Application/Foo.ContentManagement

Apply patches from 'Packages/Application/Foo.ContentManagement/Patches'