<?php
/**
 * App.php contains the implementation of the Konekt Framework's Application class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2011 - 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     6 2012-05-12
 * @since       2011-12-11
 *
 */


/**
 * The Core Application Class
 * 
 * It handles some basic settings, config, modules, Smarty and Doctrine
 * along with the Database connection.
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 */
class Konekt_Framework_Core_Model_App{

   const ANY_COUNTRY             = 'XX';

   /**
    * Root Directory
    *
    * @var string
    */
   protected $_rootDir;
   
   /**
    * App Base Directory
    *
    * @var string
    */
   protected $_appDir;
   
   /**
    * Var Base Directory
    *
    * @var string
    */
   protected $_varDir;

   /**
    * Etc Base Directory
    *
    * @var string
    */
   protected $_etcDir;
   
   /**
    * Lib Base Directory
    *
    * @var string
    */
   protected $_libDir;
   
   /**
    * Code Base Directory
    *
    * @var string
    */
   protected $_codeDir;
   
   /**
    * The Configuration
    *
    * @var Konekt_Framework_Core_Model_Config
    */
   protected $_config;
   
   /**
    * The list of application modules
    *
    * @var array
    */
   protected $_modules;
   
   /**
    * The Country setting for the Application (session)
    *
    * @var string
    */
   protected $_country = self::ANY_COUNTRY;
   
   /**
    * The Language setting for the Application
    *
    * @var string
    */
   protected $_language;
   
   /**
    * The Request Singleton
    *
    * @var Konekt_Framework_Core_Model_Request
    */
   protected $_request = NULL;
   
   /**
    * The Response Singleton
    *
    * @var Konekt_Framework_Core_Model_Response
    */
   protected $_response = NULL;
   
   /**
    * The Router Singleton
    *
    * @var Konekt_Framework_Core_Model_Router
    */
   protected $_router = NULL;
   
   /**
    *
    * @var Doctrine_Manager
    */
   public $doctrineManager;
   
   /**
    *
    * @var Doctrine_Connection
    */
   public $connection;
   
   /**
    * Sets up the Doctrine Classes and loaders
    *
    */
   private function setupDoctrineClasses()
   {
      require_once ($this->_libDir . DS . 'Doctrine.php');
      spl_autoload_register(array('Doctrine', 'autoload'));
      spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
   }

   
   /**
    * Sets up the Doctrine Connection
    *
    */
   private function setupDoctrineConnection()
   {
      $this->doctrineManager = Doctrine_Manager::getInstance();
      $this->doctrineManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING,
         Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

      $dbcfg = $this->_config->getValue('core/db');
      if (!$this->_config->dbType || $this->_config->dbType == 'none')
         return false;
      $this->connection = Doctrine_Manager::connection(
         $this->_config->dbType . '://' .
         $this->_config->dbUser . ':' .
         $this->_config->dbPassword . '@' .
         $this->_config->dbHost . '/' .
         $this->_config->dbDatabase,
         'doctrine'
         );
      $this->connection->setCharset("utf8");
   }
   
   
   /**
    * Gets the list of modules in the current installation
    *
    * @return array The array of installed modules
    */
   public function getModules()
   {
      if (!$this->_modules)
      {
         $this->_modules = $this->_config->getValue('core/modules');
      }
      return $this->_modules;      
   }
   
   /**
    * Returns the Module directory based on the Module name.
    * 
    * @param   string   $moduleName The Name of the module
    * 
    * @return  string
    */
   public function getModuleDirectory($moduleName)
   {
      return $this->_codeDir . DS . str_replace('_', DS, $moduleName);
   }
   
   /**
    * Initializes a Module.
    * 
    * @param   string   $moduleName The Name of the module
    * 
    * @return  void
    */
   private function initModule($moduleName)
   {
      $modDir = $this->getModuleDirectory($moduleName);
      /** Loads Module's Doctrine Entities if there are any, and if sql is enabled */
      $entDir = $modDir . DS . Konekt_Framework_Core_Model_Config::DOCTRINE_ENTITIES_DIR;

      if (is_dir($entDir) && ! $this->_config->getValue('core/nosql'))
      {
         Doctrine_Core::loadModels($entDir);
      }
      /** Adds Module's Smarty Directory */
      $tplDir = $modDir . DS . Konekt_Framework_Core_Model_Config::SMARTY_REL_DIR;
      if (is_dir($tplDir))
      {
         $this->getResponse()->addTemplateDir($tplDir);
      }
      
      /** Invokes the Module Init Class if exists */
      $initClass = $moduleName . '_Init';
      if (class_exists($initClass)) {
         $initClass::init($modDir);
      }
   }
   
   
   /**
    * Starts a new named php session
    *
    * @param string $name The session name
    * @param int $expire The expiration in minutes. If omitted, php default value (180min at the moment of writing) will be used
    *
    * @return bool True on success
    */
   public function startSession($name, $expire = NULL)
   {
      session_name($name);
      if ($expire)
         session_cache_expire($expire);
      return session_start();
   }
   
   
   /**
    * Closes the current session altogether. Unsets all session values and also sets the Session
    * cookie to expire immediately.
    * 
    * @return void
    * 
    */
   public function closeSession()
   {
      $_SESSION = array();

      if (ini_get("session.use_cookies"))
      {
         $params = session_get_cookie_params();
         setcookie(session_name(), '', time() - 42000,
               $params["path"], $params["domain"],
               $params["secure"], $params["httponly"]
               );
      }

      session_destroy();
   }
   
   
   /**
    * Checks whether session has been started or not. Check is being done by looking up for the session id.
    * 
    * @return  bool  Returns true if session exists, false otherwise
    */
   public function isSessionStarted()
   {
      $sid = session_id();
      return !empty($sid);
   }
   
   /**
    * Sets a Session Variable Value
    *
    * @param string $name The variable name
    * @param string $value The variable value
    *
    * @return Konekt_Core_Model_App Returns a pointer to itself
    */
   public function setSessionValue($name, $value)
   {
      $_SESSION["$name"] = $value;
      return $this;
   }
   
   /**
    * Returns a Session variable value
    *
    * @param string $name     The Variable name
    * @param mixed  $default  The default value to return if value is not set in session
    *
    * @return mixed Returns the value stored in session
    */
   public function getSessionValue($name, $default = null)
   {
      return isset($_SESSION["$name"]) ? $_SESSION["$name"] : $default;
   }
 
   /**
    * Initializes the Konekt Application including Doctrine and Smarty
    *
    * @param string $appDir      The app directory
    * @param string $varDirName  The name of the var directory (usually `var`)
    * @param string $etcDirName  The name of the etc directory (usually `etc`)
    * @param string $libDirName  The name of the lib directory (usually `lib`)
    * @param string $codeDirName The name of the code directory (usually `code`)
    *
    */  
   public function init($appDir, $varDirName, $etcDirName, $libDirName, $codeDirName)
   {
      $this->_appDir  = $appDir;
      $this->_varDir  = $this->_appDir . DS . $varDirName;
      $this->_etcDir  = $this->_appDir . DS . $etcDirName;
      $this->_codeDir = $this->_appDir . DS . $codeDirName;
      $this->_libDir  = $this->_codeDir . DS
         . Konekt::helper('core')->getMyVendor($this)  . DS
         . Konekt::helper('core')->getMyPackage($this) . DS
         . Konekt::helper('core')->getMyModule($this)  . DS
         . $libDirName;

      $this->setupDoctrineClasses();
      
      $this->_config = new Konekt_Framework_Core_Model_Config();
      $this->_rootDir = realpath($appDir . DS . $this->_config->getValue('core/rootDir','../'));
      
      if (!$this->_config->getValue('core/nosql')) {
         $this->setupDoctrineConnection();
      }
            
      foreach ($this->getModules() as $module => $params) {
         $this->initModule($module);
      }
   }
   
   
   /**
    * Returns the root directory.
    * 
    *
    * @return string The Document Root Directory (usually one level higher then `app`)
    */
   public function getRootDir()
   {
      return $this->_rootDir;
   }
   
 
   /**
    * Returns The Application base directory (<ROOT_PATH>/app/) without trailing slash
    *
    */  
   public function getAppDir()
   {
      return $this->_appDir;
   }

   /**
    * Returns The Variables directory (<ROOT_PATH>/app/var) without trailing slash
    *
    */  
   public function getVarDir()
   {
      return $this->_varDir;
   }

   /**
    * Returns The Etc directory (<ROOT_PATH>/app/etc) without trailing slash
    *
    * @return string
    */  
   public function getEtcDir()
   {
      return $this->_etcDir;
   }
   
   /*
    * Returns The Lib directory without trailing slash
    *
    * @param object|NULL  $object The Object that wants to know its own libdir. In case it's null, the Konekt package's libdir will be returned.
    * @return string
    */  
   public function getLibDir($object = NULL)
   {
      if (NULL == $object || !is_object($object))
      {
         return $this->_libDir;
      }
      else
      {
         return $this->_codeDir . DS
            . Konekt::helper('core')->getMyVendor($object)  . DS
            . Konekt::helper('core')->getMyPackage($object) . DS
            . Konekt::helper('core')->getMyModule($object)  . DS
            . Konekt::LIB_ROOT_DIR;
      }
   }
   
   /**
    * Returns the Config Singleton. In case it's not initialized, it
    * gets created. Note that this should happen during init().
    *
    * @return Konekt_Framework_Core_Model_Config The initialized config object
    */
   public function getConfig()
   {
      if (!$this->_config)
      {
         $this->_config = new Konekt_Framework_Core_Model_Config();
      }
      return $this->_config;
   }
   
   /**
    * Shortcut to getting to the config values
    *
    * @param string $name The config entry name (key)
    *
    * @return mixed The value returned by the global config (Konekt_Core_Model_Config) instance's getValue() function
    */
   public function getConfigValue($name)
   {
      return $this->getConfig()->getValue($name);
   }
   
   /**
    * Return true if the application was run from the command line, and false if from a browser
    *
    * @return bool
    */   
   public function runningFromCli()
   {
      return empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0;
   }
   
   /**
    * Returns the currently set Country for the application
    *
    */
   public function getCountry()
   {
      return $this->_country;
   }
   
   /**
    * Sets the Country for the application
    *
    * @param string $countryCode
    *
    * @return Konekt_Framework_Core_Model_App Returns the self instance
    */
   public function setCountry($countryCode)
   {
      $this->_country = $countryCode;
      return $this;
   }
   
   /**
    * Returns the currently set Language for the application
    *
    */
   public function getLanguage()
   {
      return $this->_language;
   }
   
   /**
    * Sets the Language for the application
    *
    * @param string $langCode
    *
    * @return Konekt_Framework_Core_Model_App Returns the self instance
    */
   public function setLanguage($langCode)
   {
      $this->_language = $langCode;
      return $this;
   }
   
   /**
    * Returns the Request Singleton. For non web-based applications returns NULL
    *
    * @return Konekt_Framework_Core_Model_Request
    */
   public function getRequest()
   {
      if (!$this->_request)
      {
         if (!$this->runningFromCli())
         {
            $this->_request = new Konekt_Framework_Core_Model_Request();
         }
      }
      
      return $this->_request;
   }
   
   /**
    * Returns the Response Singleton
    *
    * @return Konekt_Framework_Core_Model_Response
    */
   public function getResponse()
   {
      if (!$this->_response) {
         $this->_response = new Konekt_Framework_Core_Model_Response();
      }
      
      return $this->_response;
   }
   
   /**
    * Returns the Router Singleton
    *
    * @return Konekt_Framework_Core_Model_Router
    */
   public function getRouter()
   {
      if (!$this->_router) {
         $this->_router = new Konekt_Framework_Core_Model_Router();
      }
      
      return $this->_router;
   }
   
   

}
