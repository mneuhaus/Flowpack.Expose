Property Configurations
#######################


Filter
******
By Tagging a Property with this Tag the Expose Interface will try to provide a Selectbox to Filter the List View by the Possible Values of this Property

**Class Reflection**::

    /**
     * @var string 
     * @Expose\Annotations\Filter
     */
    protected $author;

**YAML**::

    TYPO3\Blog\Domain\Model\Blog: 
        Properties:
            Author: 
                Filter: true

Label
*****
With this Tag you can set the Label for a Property which is by Default a CamelCased version of the propertyname

**Class Reflection**::

    /**
     * @var string
     * @Expose\Annotations\Label("Post Title")
     */
     protected $title;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Title:
                Label: Post Title

Ignore
******
There are a number of Properties which have no use to be Exposeistrated through a GUI. With the ignore Tag you can control the Visibility of the Property to the Expose Interface.

Ignore the Property Completly
=============================
**Class Reflection**::

    /**
     * @var string
     * @Expose\Annotations\Ignore 
     */
    protected $id;
    
**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Id:
                Ignore: true

Ignore the Property in specific Views
=====================================
**Class Reflection**::

    /**
     * @var string
     * @Expose\Annotations\Ignore list,view */
    protected $content;


**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Content:
                Ignore: list,view
                
Infotext
********
For more Information about a Property aside from the Title you can provide an Infotext that will be shown beside/below the Input Widgets in the Create and Update Views

**Class Reflection**::

    /**
     * @var string
     * @Expose\Annotations\InfoText("Please tell us who you are")
     */
    protexted $author;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Author:
                Infotext: Please tell us who you are

Inline
********
Through this annotation you can make the Child Entities of relations editable directly on the editing page of the parent entity
There are 2 different variants included for this: Default and Tabular. See Variants for more informations.
Make sure to add "cascade={"all"}" to your ORM relation, because otherwise you'll get an error when trying to save

**Class Reflection**::

    /**
     * @var \ExposeDemo\Domain\Model\Address
     * @ORM\ManyToOne(cascade={"all"})
     * @Expose\Inline()
     */
    protected $address;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Address:
                Inline: True


OptionsProvider
***************

**Class Reflection**::

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<\Expose\Security\Policy>
     * @Expose\Annotations\OptionsProvider("\Expose\OptionsProvider\PolicyOptionsProvider")
     */
    protected $grant;

**YAML**::

    TYPO3\Blog\Domain\Model\Blog: 
        Properties:
            Grant: 
                OptionsProvider: \Expose\OptionsProvider\PolicyOptionsProvider 


Representation
**************
Through this option you can set options for the Representation of an property.
Currently it is only used for the datetimeFormat

**Class Reflection**::

    /**
     * @var \Datetime
      * @Expose\Representation(datetimeFormat="Y-m-d")
     */
    protected $date;

**YAML**::

    Expose\Domain\Model\Widgets: 
        Properties:
            date: 
                Repesentation: 
                    datetimeFormat: Y-m-d


Title
*****
This option only works if you use the MagicModel!
You can Specify any Property that can be Converted to a String as a Title to be used as a simple String Repesentation which is for example used in the Single and Multiple Relation Widget to Identify an Model Item

The MagicModel will try the following things to determine a title:

1. Does the Model Provide a __toString() Method  
2. Does one or more @title Tags exist  
3. Are there Properties Tagged as @identity which can be converted to String 
4. Is there a Property with the name "title" or "name"

**Class Reflection**::

    /**
     * @var string 
     * @Expose\Annotations\Title
     */
    protected $title;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Title: 
                Title: true

First thing that matches will be used in the Order specified


Validate
********
Please Check the FLOW3 Documentation for the Validation rule: 
http://flow3.typo3.org/documentation/guide/partii/validation.html

Variant
********
Variants are different Variations for a similar use-case.
Included are Variants for the InlineEditing:

Default
    The default Stacked Form-View

Tabular
    In this variant the inputs are aligned in a table to take up less space.

**Class Reflection**::

    /**
     * @var \ExposeDemo\Domain\Model\Address
     * @ORM\ManyToOne(cascade={"all"})
     * @Expose\Inline()
     * @Expose\Variant("Tabular")
     */
    protected $address;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Address:
                Inline: True
                Variant: Tabular


Widget
******
Instead of the automatically Assigned Widget you can use this Tag to specify a specific Widget.

**Class Reflection**::

    /**
     * @var string
     * @Expose\Annotations\Widget("TextArea")
     */
     protected $content;

**YAML**::

    TYPO3\Blog\Domain\Model\Post: 
        Properties:
            Content:
                Widget: TextArea