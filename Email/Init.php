<?php
/**
 * The Email Module Initialization File
 * 
 * This Email Module is mostly a wrapper around the PhpMailer 5.2.1 classes.
 * PhpMailer can be found at: http://code.google.com/a/apache-extras.org/p/phpmailer/
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Email
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-05-28
 * @since       2012-05-28
 *
 */
 
/**
 * The Email Module Initialization Class
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Email
 */
class Konekt_Framework_Email_Init{
   
   const PHPMAILER_DIRECTORY = 'PHPMailer';
   /**
    * Sets up the path to the embedded PHPMailer classes
    * 
    * @param   string   $moduleDirectory
    * 
    * @return  bool
    */
   public static function init($moduleDirectory)
   {
      set_include_path( $moduleDirectory . DS . Konekt::LIB_ROOT_DIR . DS
                        . self::PHPMAILER_DIRECTORY . PATH_SEPARATOR
                        . get_include_path()
                     );
      return true;
   }
}
