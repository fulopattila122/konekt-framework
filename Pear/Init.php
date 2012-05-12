<?php
/**
 * The Pear Module Initialization File
 * 
 * This Pear Module contains all the Pear modules required by the Konekt Framework, in case
 * Pear is not present on the host server.
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Pear
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-05-12
 * @since       2012-05-12
 *
 */
 
/**
 * The Pear Module Initialization Response Class
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Pear
 */
class Konekt_Framework_Pear_Init{
   
   /**
    * Sets up the path to the embedded Pear classes
    * 
    * @param   string   $moduleDirectory
    * 
    * @return  bool
    */
   public static function init($moduleDirectory)
   {
      set_include_path( $moduleDirectory . DS . Konekt::LIB_ROOT_DIR . PATH_SEPARATOR
                        . get_include_path()
                     );
      return true;
   }
}
