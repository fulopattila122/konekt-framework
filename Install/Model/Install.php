<?php
/**
 * Install.php contains the implementation of Installer class 
 *
 * 
 * @category    Konekt
 * @package     Framework
 * @subpackage  Install
 * @copyright   Copyright (c) 2011 - 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     5 2012-07-12
 * @since       2011-12-12
 *
 */


/**
 * Class for installing Application and Modules
 *
 * @category    Konekt
 * @package     Framework
 */
class Konekt_Framework_Install_Model_Install
{
   const SCHEMA_FILE    = 'schema.yml';
   const FIXTURE_FILE   = 'data.yml';

   /**
    * Installs a single Module
    *
    * @param   string   $moduleName       The name of the module to install
    * @param   bool     $doDbOperations   If true database will be created (from models) and fixtures will be loaded (from data.yml)
    * @param   bool     $generateModels   If true the Model classes will be (re)generated from schema.yml
    *
    * @return  bool     Returns true if operation was successful, false on failure.
    */
   public function installModule($moduleName, $doDbOperations = true, $generateModels = true)
   {
      try
      {
         $modDir    = Konekt::app()->getModuleDirectory($moduleName);
         $confDir   = $modDir . DS . Konekt_Framework_Core_Model_Config::CONF_REL_DIR;
         $modelsDir = $modDir . DS . Konekt_Framework_Core_Model_Config::DOCTRINE_ENTITIES_DIR;

         /* Install Schema */
         if (file_exists($confDir . DS . self::SCHEMA_FILE))
         {
            if ($generateModels)
               Doctrine_Core::generateModelsFromYaml($confDir . DS . self::SCHEMA_FILE, $modelsDir);
               
            if ($doDbOperations)
               Doctrine_Core::createTablesFromModels($modelsDir);
         }
         
         /* Install Initial Data fixtures */
         if ($doDbOperations && file_exists($confDir . DS . self::FIXTURE_FILE))
         {
            Konekt::app()->connection->exec('set names utf8');
            Doctrine_Core::loadData($confDir . DS . self::FIXTURE_FILE);
         }
      }
      catch(Exception $e)
      {
         echo "Failure: " . $e->getMessage() . "\n";
         return false;
      }
      return true;
   }

}
