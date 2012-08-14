<?php
/**
 * The Core Module Initialization File
 * 
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-08-14
 * @since       2012-08-14
 *
 */
 
/**
 * The Core Module Initialization Class
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Init{
   
   /**
    * Sets up the path to the embedded Pear classes
    * 
    * @param   string   $moduleDirectory
    * 
    * @return  bool
    */
   public static function init($moduleDirectory)
   {
      if (version_compare(PHP_VERSION, '5.3.0', '<')) {
         include_once('compatibility52.php');
      }
      
      if (version_compare(PHP_VERSION, '5.2.0', '<') && get_magic_quotes_gpc()) {
         throw new Exception('Magic quotes are on. They must be disabled in order to Konekt Framework to operate properly');
      }
      
      return true;
   }
}
