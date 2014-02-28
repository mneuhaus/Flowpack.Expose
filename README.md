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

## Required pending ChangeSets

create an 'gerrit.json' file with this content in your project root:

```
{
	"TYPO3.Flow": {
		"[WIP][FEATURE] Add an isDefaultView matcher for ViewConfiguration": "25147",
		"[WIP][FEATURE] Add a way to clear caches by Path + FilePattern": "25078"
	}
}
```

Then run the following command in your root directory:

```
beard patch
```

**beard** is a little helper to automatically patch based on gerrit
changes specified in gerrit.json. (https://github.com/mneuhaus/Beard)