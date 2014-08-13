# Flowpack.Expose

WIP Documentation: http://flowpackexpose.readthedocs.org/en/latest/

**Example ListView:**

![](http://dl.dropbox.com/u/314491/Screenshots/vv6gdteg95ea.png)

## Installation

add this repository to your composer.json:

```
    {
        "type": "vcs",
        "url": "git@github.com:mneuhaus/Flowpack.Expose.git"
    }
```

```
composer require flowpack/expose
```

## Required pending ChangeSets

create an 'gerrit.json' file with this content in your project root:

```
{
	"TYPO3.Flow": {
		"[WIP][FEATURE] Add a way to clear caches by Path + FilePattern": "25078"
	},
	"TYPO3.Fluid": {
		"!!![WIP][FEATURE] ControllerInheritence Fallbacks": "31939"
	}
}
```

Then run the following command in your root directory:

```
beard patch
```

**beard** is a little helper to automatically patch based on gerrit
changes specified in gerrit.json. (https://github.com/mneuhaus/Beard)
