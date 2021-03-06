<?php
/**
 * Abstract.php contains the Abstract Entity Model Class
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     2 2012-09-17
 * @since       2012-07-29
 *
 */


/**
 * The Common Entity Abstract model class for wrapping Doctrine Entities
 *
 * You might be looking for magic getter and setter methods that was the first
 * intention here as well. After reading this:
 * http://blog.webspecies.co.uk/2011-05-23/the-new-era-of-php-frameworks.html
 * I decided to omit the magic methods. So in concrete classes you have to do the
 * tedious work of mapping getters and setters one by one, but according to many other people
 * it's worth it.
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
abstract class Konekt_Framework_Core_Model_Entity_Abstract {
   
   /** @var string The Name of the Entity Class in Doctrine. This must be defined by concrete classes */
   protected $entityName;
   
   /** @var Doctrine_Record The internal underlying Doctrine_Record derived class instance */
   protected $_entity;
   
   /**
    * Checks whether the internal Doctrine Entity is initialized and creates a new Instance if necessary
    *
    * @return bool True if record existed prior to the function call, false if it was initialized during the call
    */
   protected function _checkRecordInstance()
   {
      if (!$this->_entity)
      {
         $this->_entity = new $this->entityName();
         return false;
      }
      else
         return true;      
   }
   
   
   public function getId()
   {
      return $this->_entity ? $this->_entity->id : NULL;
   }
   
      
   /**
    * Loads an Entity by its id
    *
    * @return Konekt_Framework_Core_Model_Entity_Abstract|false  Returns the Instance or false if failed to load
    */
   public function load($id)
   {
      $this->_checkRecordInstance();
      $result = $this->_entity->getTable()->find($id);
      if ($result) {
         $this->_entity = $result;
         return $this;
      } else {
         return false;
      }
   }
   
   
   /**
    * Saves the changes to the storage database
    *
    * @return Konekt_Framework_Core_Model_Entity_Abstract  Returns the self reference
    */
   public function save()
   {
      if ($this->_checkRecordInstance())
      {
         $this->_entity->save();
      }
      return $this;
   }
   
   /**
    * Deletes the Entity
    *
    * @return bool   Returns true if deleted successfully
    */
   public function delete()
   {
      if ($this->_checkRecordInstance()) {
         return $this->_entity->delete();
      } else {
         return false;
      }
   }
   
   
   /**
    * Checks whether an entity with the given id exists
    *
    * @return bool
    */
   public static function exists($id)
   {
      $entity = Doctrine_Core::getTable($this->entityName)->find($id);
      return $entity ? true : false;
   }
   
   /**
    * Retruns the Entity Record in Array format
    *
    * @return array|bool   Returns array on success false if record is not initialized
    */
   public function toArray()
   {
      if ($this->_checkRecordInstance()) {
         return $this->_entity->toArray();
      } else {
         return false;
      }
   }
   
}
