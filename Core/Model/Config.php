<?php
/**
 * Config.php contains the implementation of the Core Config Model class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2011 - 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     $Revision-Id$ $Date$
 * @since       2011-12-12
 *
 */


/**
 * Model Class for the Application's Configuration
 *
 * @category    Konekt
 * @package     Framework
 */

class Konekt_Framework_Core_Model_Config{

   const LOCAL_CONFIG            = 'local.yml';
   const CONF_REL_DIR            = 'etc';
   const SMARTY_REL_DIR          = 'View/templates';
   const DOCTRINE_ENTITIES_DIR   = 'Model/Doctrine';

   /**
    * @var array
    */
   protected $_config;
   
   public $dbHost;
   public $dbUser;
   public $dbPassword;
   public $dbDatabase;
   public $dbType;
   
   function __construct()
   {
      $cfgFile = Konekt::app()->getEtcDir() . DS . self::LOCAL_CONFIG;
      if (!file_exists($cfgFile)) {
         throw new Exception("Config file doesn't exist");
      }
      $this->_config = sfYaml::load($cfgFile);
      if (isset($this->_config['core']['db']['type']) && $this->_config['core']['db']['type'] !== 'none')
      {
         $this->dbHost     = Konekt::helper('core')->decrypt($this->_config['core']['db']['host']);
         $this->dbUser     = Konekt::helper('core')->decrypt($this->_config['core']['db']['username']);
         $this->dbPassword = Konekt::helper('core')->decrypt($this->_config['core']['db']['password']);
         $this->dbDatabase = Konekt::helper('core')->decrypt($this->_config['core']['db']['database']);
         $this->dbType     = Konekt::helper('core')->decrypt($this->_config['core']['db']['type']);
      }
   }

   /**
    * Returns a value from the configuration
    * 
    * @param string $name     The config entry key name
    * @param string $default  The default value to return in case the value is not set
    * 
    * @return mixed Returns the value(s) based on the config yaml file.
    *    In case the config entry is not set, returns $default (that defaults to null)
    */
   public function getValue($name, $default = null)
   {
      $node = $this->_config;
      foreach (explode('/', $name) as $key)
      {
         $node = isset($node["$key"]) ? $node["$key"] : null;
      }
      return isset($node) ? $node : $default;
   }
   
}
