<?php
/**
 * Markdown.php contains the wrapper Helper class for the Markdown parser
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-09-20
 * @since       2012-09-20
 *
 */


/**
 * The Markdown Helper Class wrapping the external Markdown parser
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Helper_Markdown extends Konekt_Framework_Core_Helper_Abstract{
   
   /**
    * The class constructor, that makes sure the external markdown library is included
    */
   public function __construct()
   {
      require_once (Konekt::app()->getLibDir() . DS . 'Markdown/markdown.php');
   }
   
   /**
    * Converts a Markdown text to Html
    * It also converts the Markdown Extra tags
    * @see http://michelf.ca/projects/php-markdown/extra/
    *
    * @param string $mdText   The Markdown text
    *
    * @return string The text in Html format
    */
   public function parse($mdText)
   {
      return Markdown($mdText);
   }
   
   
}
