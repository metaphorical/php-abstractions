#PHP Abstractions

Collection of battle tested solutions for various problems in different PHP apps I worked on. Most of the apps were data driven.

**Built with free, abstract, form in mind**

##Abstract Entities (and collections of those)

 Abstraction of Entity - Domain Object Modeling class that ensures proper approach to DTO integrity, DTO creation and and easy bussines logic requirements description.

 Abstract Entity Collection - For collections of defined entities, allows for more advanced and yet controlled entity collection manipulation
 
###Entities 

  Any class extending AbstractEntity allows for definition of allowed fields and mandatory fields in form of array of field name strings.
 
  **Definition of DTO consistency rule**: Every allowed field must have defined default value.

####Definition of business rules dependencies:
  
  * Every mandatory field must be allowed field
  * Every mandatory field must have custom value passed through $data array

Custom DTO is defined by extending AbstractEntity class and defining allowed fields and their default values...

Anything else is arbitrary depending on business logic dependencies.

####When DTO is defined, setters and getters are available in form:
                         -set<Upper case first letter name of allowed field>(<field name>)
                         -get<Upper case first letter name of allowed field>(<field name>)


There are also *toArray()* and *toJSON()* methods to get full  data object in defined form.

###Collection

**AbstractCollection** class is create to receive array of classes that inherit AbstractEntity, but can be used for any class entities.

Collection is defined by creating a class that **extends AbstractEntityCollection** abstract class and defining entity class of collection members as a string value of  protected $_entityClass variable.

####It encapsulates methods for manipulating collections of entities:

           -getEntities()               -Returns a array of entities

           -clear()                     -Clears Collection

           -rewind()                    -Resets Collection pointer to first member of a collection

           -current()                   -Returns current member of a Collection

           -next()                      -Returns next member of a collection

           -key()                       -Returns key of current Collection member

           -valid()                     -checks for existance of current member of Collection - used for checking if there are more members

           -count()                     -returns Collection member count

           -offsetSet($key, $entity)    -Sets passed entity as member of Collection on place defined by key value, if it is of class that is defined as memeber entity class

           -offsetUnset($key)           -Removes Collection member from place defined by key

           -offsetGet($key)             -Returns member of a Collection from place defined by key value

           -offsetExists($key)          -Checks if entity on a certain place exists