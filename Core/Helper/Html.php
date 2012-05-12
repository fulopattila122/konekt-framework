<?php
/**
 * Html.php contains the implementation of core Html Helper class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     3 2012-05-12
 * @since       2012-02-14
 *
 */


/**
 * The core Html Helper Class containing utilities for processing html files
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Helper_Html extends Konekt_Framework_Core_Helper_Abstract{
   
   /**
    * Returns the array of images and their attributes from an html code snippet
    *
    * @param string $htmlContent The html source
    *
    * @return array The retrieved array
    */
   public function getImages($htmlContent)
   {
      preg_match_all('/<img[^>]+>/i', $htmlContent, $result);
      
      $img = array();
      $i = 0;
      foreach( $result[0] as $img_tag)
      {
          preg_match_all('/(alt|title|src)=("[^"]*")/i',$img_tag, $img[$i]);
          $i++;
      }
      return $img;      
   }
   
   
}
