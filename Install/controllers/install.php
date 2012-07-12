<?php

require_once("../../Konekt.php");

   function recreateDatabase()
   {
      echo "Dropping database...";
      Doctrine_Core::dropDatabases();
      echo "OK.\n";

      echo "Creating database...";
      Doctrine_Core::createDatabases();
      echo "OK.\n";
   }
   
   /**
    * Installs a single Application Module
    *
    * @param string                       $module              The name of the module
    * @param Konekt_Install_Model_Install $installer           The Installer Model instance
    * @param bool                         $installDatabase     Whether or not to do Database operations
    * @param bool                         $generateModels      Whether or not to generate Model classes
    *
    * @return bool                        Returns the value that was returned by the Installer Model instance
    */
   
   function _installModule($module, $installer, $installDatabase, $generateModels)
   {
      if ($installDatabase) {
         $opText = 'Installing';
         $opText .= $generateModels ? ' and generating models for' : ' database for';
      } else {
         if ($generateModels) {
            $opText = 'Generating models for';  
         } else {
            echo "No operation specified for $module, skipping.\n";
            return false;
         }
      }
      
      echo "$opText module $module...";
      $result = $installer->installModule($module, $installDatabase, $generateModels);
      echo $result ? "OK.\n" : "FAILED.\n";      
      return $result;
   }

   function install($doSql, $doModels, $singleModule = null)
   {
      $installer = new Konekt_Framework_Install_Model_Install();
      
      if ($singleModule)
      {
         _installModule($singleModule, $installer, $doSql, $doModels);
      }
      else
      {
         foreach (Konekt::app()->getModules() as $module => $params)
            _installModule($module, $installer, $doSql, $doModels);
      }
   }
   
   function displayModuleList()
   {
      foreach (Konekt::app()->getModules() as $module => $params)
         echo "$module\n";
   }
   
   function displayHelp()
   {
      echo "Konekt Framework Command Line Installer.\nUsage:\n";
      echo "   -i:\tInstall: Generates DB Tables from Schema and loads fixtures\n";
      echo "   -d:\tDrop database: Drop database (before install)\n";
      echo "   -g:\tGenerate Model Classes from yaml\n";
      echo "   -l:\tList Modules: Display the list of the Module Names\n";
      echo "   -h:\tDisplay this help text\n";
      echo "   --module <modulename>\tOnly install a single module\n";        
   }

   if (Konekt::app()->runningFromCli())
   {
      $opt = getopt("idglh", array("module:"));
      
      if (empty($opt) || isset($opt['h'])) {
         displayHelp();
         exit(0);
      }
      
      
      if (isset($opt['l'])) {
         displayModuleList();
         exit(0);
      }
      
      $module = isset($opt['module']) ? $opt['module'] : null;
      
      if (isset($opt['d'])) recreateDatabase();
      
      $doSql    = isset($opt['i']);
      $doModels = isset($opt['g']);
      if ($doModels || $doSql) {
         install($doSql, $doModels, $module);
         exit(0);
      } elseif (!isset($opt['d'])) {
         exit("You did not specify any command\n");
      }
   }
