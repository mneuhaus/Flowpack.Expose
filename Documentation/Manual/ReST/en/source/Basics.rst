Basic Usage
###########

Installation
************
If you don't have a FLOW3 Project set up yet take a look at this:
http://flow3.typo3.org/documentation/quickstart.html

Installing Expose::
    
    cd %FLOW3-Project-Directory%
    git clone git@github.com:mneuhaus/FLOW3-Expose.git Packages/Application/Expose
    ./flow3 package:activate Expose
    ./flow3 doctrine:migrate

Adding ExposeDemo as well::

    cd %FLOW3-Project-Directory%
    git clone git@github.com:mneuhaus/FLOW3-ExposeDemo.git Packages/Application/ExposeDemo
    ./flow3 package:activate ExposeDemo
    ./flow3 doctrine:migrate

Quick start
***********

There are 2 Ways to Configure the Expose Interface: 

1. Settings.yaml
2. Class Reflections inside the Models

	**Note:** The Settings.yaml overrules the Class Reflections in order to make it Possible to change the Behaviour of 3rd Party Packages without messing with external Code.  

**Settings.yaml**::

    Expose:
        Beings: 
            \TYPO3\Blog\Domain\Model\Post:
                Active: true 
                Properties:
                    content:
                        Widget: TextArea

This Example Activates the Post model of the Blog Example (autoexpose:true) and Changes the Widget for the Content Property from a simple Textfield to a Textarea

**Class Reflections**::

    use Expose\Annotations as Expose;
    /**
     * A blog post
     * ...
     * @Expose\Active 
     */
    class Post { 
        /**
         * @var string
         * @Expose\Widget("TextArea")
         */
        protected $content;
    }

This Example Does the exact same thing as the Settings.yaml Example but this time inside the Post.php file with the Tag @Expose\Active and @Expose\Widget("TextArea")
