# Expose Module for TYPO3.Flow using TYPO3.Form

## Installation

add this repository to your composer.json:

```
    {
        "type": "vcs",
        "url": "git@github.com:mneuhaus/TYPO3.Expose.git"
    }
```

```
composer require typo3/expose
```

**Apply the following changesets:**

```
	# TYPO3.TypoScript:
	# [!!!][FEATURE] re-implement Processors based on TypoScript Objects and Eel
	# https://review.typo3.org/#/c/24423/
	cd Packages/Application/TYPO3.TypoScript/
	git fetch git://git.typo3.org/Packages/TYPO3.TypoScript refs/changes/23/24423/9 && git cherry-pick FETCH_HEAD

```
