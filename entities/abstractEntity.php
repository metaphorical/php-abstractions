<?php

/**
*  Abstraction of Entity - Domain Object Modeling class that ensures proper approach to DTO integrity, DTO creation and
* and easy bussines logic requirements description.
*
*   Abstract Entity Collection - For collections of defined entities, allows for more advanced and yet controlled entity collection manipulation
*
* @author   Rastko - rastko.vukasinovic@gmail.com
*
*
*============================================================================================================================================
*          AbstractEntity
*============================================================================================================================================
*
*  Any class extending AbstractEntity allows for definition of allowed fields and mandatory fields in form of array of field name strings.
*
*  Definition of DTO consistency rule: Every allowed field must have defined default value.
*
*  Definition of business rules dependencies:
*        -Every mandatory field must be allowed field
*        -Everu mandatory field mus have custom value passed through $data array
*
*  Custom DTO is defined by extending AbstractEntity class and defining allowed fields and their default values...
* Anything else is arbitrary depending on bussines logic dependencies.
*
*  When DTO is defined, setters and getters are available in form:
*                         -set<Upper case first letter name of allowed field>(<field name>)
*                         -get<Upper case first letter name of allowed field>(<field name>)
*
*  There are also toArray() and toJSON() methods to get full  data object in defined form.
*
*
*
*
*
*===========================================================================================================================================
*          AbstractCollection
*===========================================================================================================================================
*
*
*  AbstractCollection class is create to recive array of classes that inherit AbstractEntity, but can be used for any class entities.
*
*  Collection is defined by creating a class that extends AbstractEntityCollection abstract class and defining entity class of collection members as a
*string value of  protected $_entityClass variable.
*
*  It encapsulates methods for manipulating collections of entities:
*
*           -getEntities()      -Returns a array of entities
*
*           -clear()         -Clears Collection
*
*           -rewind()        -Resets Collection pointer to first member of a collection
*
*           -current()       -Returns current member of a Collection
*
*           -next()          -Returns next member of a collection
*
*           -key()           -Returns key of current Collection member
*
*           -valid()         -checks for existance of current member of Collection - used for checking if there are more members
*
*           -count()         -returns Collection member count
*
*           -offsetSet($key, $entity)    -Sets passed entity as member of Collection on place defined by key value, if it is of class that is defined as memeber entity class
*
*           -offsetUnset($key)       -Removes Collection member from place defined by key
*
*           -offsetGet($key)         -Returns member of a Collection from place defined by key value
*
*           -offsetExists($key)      -Checks if entity on a certain place exists
*
*
*
*/


abstract class AbstractEntity
{
    /**
    * Entity describers
    */

    protected $_values = array();          // Values

    protected $_allowedFields = array();   // Array of names of allowed fields

    protected $_defaultValues = array();    // Array of default values of Allowed fields - all allowed fields must have default values fort the sake of DTO wholeness

    protected $_mandatoryFields = array();  // Array of names of mandatory fields, fields essential for the functionality are considered mandatory.
                                            // Mandatory fields have to be allowed fields

    /**
     *  Constructor for the entity
     */
    public function __construct(array $data) {
        // First check is "fool proof" check - are mandatory fields allowed
        $mandatories = $this->_mandatoryFields;
        foreach ($mandatories as $mandatory) {
            if (!in_array($mandatory, $this->_allowedFields)) {
                throw new Exception('How can field that is not allowed be mandatory?');
            }
        }

        // Counter to check for presence of all mandatory fields in data object
        $mandatoryCounter = 0;

        // Checking if default values are set for all allowed fields
        if(count($this->_defaultValues)!=count($this->_allowedFields)) {
            throw new Exception('All allowed fields need to have defaults set.');
        }

        // Setting up default values to assure wholeness of DTO entity caries
        for ($i=0; $i < count($this->_allowedFields) ; $i++) {
            $name = $this->_allowedFields[$i];
            $this->$name = $this->_defaultValues[$i];
        }

        // Setting up the real values passed through data object
        foreach ($data as $name => $value) {
            $this->$name = $value;

            // Counting existing mandatory fields
            if (!empty($this->_mandatoryFields) && in_array($name, $this->_mandatoryFields)) {
                $mandatoryCounter++;
            }
        }

        // Stopping everything if mandatory fields are missing from data object passed
        if ($mandatoryCounter != count($this->_mandatoryFields)) {
            throw new Exception('All mandatory fields must have custom values');
        }

    }

    /**
     * Assign a value to the specified field via the corresponding setter (if it exists);
     * otherwise, assign the value directly to the '$_values' protected array
     *
     * setter name is defined based on the field name it defines and read directly from the data object
     */
    public function __set($name, $value)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new Exception('The field ' . $name . ' is not allowed for this entity.');
        }
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter) && is_callable(array($this, $setter))) {
            $this->$setter($value);
        }
        else {
            $this->_values[$name] = $value;
        }
    }

    /**
     * Get the value assigned to the specified field via the corresponding getter (if it exists); otherwise, get the value directly from the '$_values' protected array
     */
    public function __get($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new Exception('The field ' . $name . ' is not allowed for this entity.');
        }
        $accessor = 'get' . ucfirst($name);
        if (method_exists($this, $accessor) && is_callable(array($this, $accessor))) {
            return $this->$accessor;
        }
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
        throw new Exception('The field ' . $name . ' has not been set for this entity yet.');
    }

    /**
     * Check if the specified field has been assigned to the entity
     */
    public function __isset($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new Exception('The field ' . $name . ' is not allowed for this entity.');
        }
        return isset($this->_values[$name]);
    }

    /**
     * Unset the specified field from the entity
     */
    public function __unset($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new Exception('The field ' . $name . ' is not allowed for this entity.');
        }
        if (isset($this->_values[$name])) {
            unset($this->_values[$name]);
        }
    }

    /**
     * Get an associative array with the values assigned to the fields of the entity
     */
    public function toArray()
    {
        return $this->_values;
    }

    /**
     * Get an JSON string created from entity
     */
    public function toJSON()
    {
        return json_encode($this->_values);
    }
}





abstract class AbstractCollection implements Iterator, Countable, ArrayAccess
{
    protected $_entities = array();
    protected $_entityClass;

    /**
     * Constructor
     */
    public function  __construct(array $entities = array())
    {
        if (!empty($entities)) {
            $this->_entities = $entities;
        }
        $this->rewind();
    }

    /**
     * Get the entities stored in the collection
     */
    public function getEntities()
    {
        return $this->_entities;
    }

    /**
     * Clear the collection
     */
    public function clear()
    {
        $this->_entities = array();
    }

    /**
     * Reset the collection (implementation required by Iterator Interface)
     */
    public function rewind()
    {
        reset($this->_entities);
    }

    /**
     * Get the current entity in the collection (implementation required by Iterator Interface)
     */
    public function current()
    {
        return current($this->_entities);
    }

    /**
     * Move to the next entity in the collection (implementation required by Iterator Interface)
     */
    public function next()
    {
        next($this->_entities);
    }

    /**
     * Get the key of the current entity in the collection (implementation required by Iterator Interface)
     */
    public function key()
    {
        return key($this->_entities);
    }

    /**
     * Check if there're more entities in the collection (implementation required by Iterator Interface)
     */
    public function valid()
    {
        return ($this->current() !== false);
    }

    /**
     * Count the number of entities in the collection (implementation required by Countable Interface)
     */
    public function count()
    {
        return count($this->_entities);
    }

    /**
     * Add an entity to the collection (implementation required by ArrayAccess interface)
     */
    public function offsetSet($key, $entity)
    {
        if ($entity instanceof $this->_entityClass) {
            if (!isset($key)) {
                $this->_entities[] = $entity;
            }
            else {
                $this->_entities[$key] = $entity;
            }
            return true;
        }
        throw new CollectionException('The specified entity is not allowed for this collection.');
    }

    /**
     * Remove an entity from the collection (implementation required by ArrayAccess interface)
     */
    public function offsetUnset($key)
    {
        if ($key instanceof $this->_entityClass) {
            $this->_entities = array_filter($this->_entities, function ($v) use ($key) {
                return $v !== $key;
            });
            return true;
        }
        if (isset($this->_entities[$key])) {
            unset($this->_entities[$key]);
            return true;
        }
        return false;
    }

    /**
     * Get the specified entity in the collection (implementation required by ArrayAccess interface)
     */
    public function offsetGet($key)
    {
        return isset($this->_entities[$key]) ? $this->_entities[$key] : null;
    }

    /**
     * Check if the specified entity exists in the collection (implementation required by ArrayAccess interface)
     */
    public function offsetExists($key)
    {
        return isset($this->_entities[$key]);
    }
}
