<?php
/**
 * The php 5.2.x compatibility unit
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
 

   if (!function_exists('lcfirst'))
   {
      /**
       * lcfirst function was introduced in php 5.3.0. This is an emulated version of it
       * 
       * Returns a string with the first character of str , lowercased if that character is alphabetic.
       * 
       * Note that 'alphabetic' is determined by the current locale. For instance, in the default "C"
       * locale characters such as umlaut-a (Š) will not be converted.
       * @param   string   $str  The input string.
       *
       * @return  string         Returns the resulting string.
       */
      function lcfirst($str)
      {
         return substr_replace($string, strtolower(substr($string, 0, 1)), 0, 1);
      }
   }
