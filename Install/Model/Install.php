<?php
/**
 * Install.php contains the implementation of Installer class 
 *
 *
 * @package     Konekt
 * @subpackage  Install
 * @copyright   Copyright (c) 2011 - 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     $Revision-Id$ $Date$
 * @since       2011-12-12
 *
 */


/**
 * Class for installing Application and Modules
 *
 * @package     Konekt
 */
class Konekt_Install_Model_Install
{
   const SCHEMA_FILE    = 'schema.yml';
   const FIXTURE_FILE   = 'data.yml';

   public function installModule($moduleName, $skipDbOperations = false)
   {
      try
      {
         $modDir    = Konekt::app()->getModuleDirectory($moduleName);
         $confDir   = $modDir . DS . Konekt_Core_Model_Config::CONF_REL_DIR;
         $modelsDir = $modDir . DS . Konekt_Core_Model_Config::DOCTRINE_ENTITIES_DIR;

         /* Install Schema */
         if (file_exists($confDir . DS . self::SCHEMA_FILE))
         {
            Doctrine_Core::generateModelsFromYaml($confDir . DS . self::SCHEMA_FILE,
               $modelsDir);
            if (!$skipDbOperations)
               Doctrine_Core::createTablesFromModels($modelsDir);
         }
         
         /* Install Initial Data fixtures */
         if (!$skipDbOperations && file_exists($confDir . DS . self::FIXTURE_FILE))
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
