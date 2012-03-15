<?php
/**
 * Browser.php contains the implementation of the Core Browser Model class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2011 - 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     $Revision-Id$ $Date$
 * @since       2011-12-12
 *
 */


/**
 * The Browser Model class
 * 
 * @category   Konekt
 * @package    Framework
 * 
 */

class Konekt_Framework_Core_Model_Browser
{
   /** @TODO: Add this advanced browser detection tool http://techpatterns.com/downloads/php_browser_detection.php */
   /** @var array */
   protected $_languages;

   /**
    * Returns an array of the languages set in the User's browser
    *
    * @return array
    */    
   public function getLanguages()
   {
      if (empty($this->_languages))
      {
         $this->_languages = array();
         foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang)
         {
            $pattern =  '/^(?P<primarytag>[a-zA-Z]{2,8})'.
                        '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'.
                        '(?P<quantifier>\d\.\d))?$/';

            $splits = array();
            if (preg_match($pattern, $lang, $splits))
            {
               $this->_languages[] = array(
                  'primarytag' => $splits['primarytag'],
                  'subtag'     => $splits['subtag'],
                  'quantifier' => $splits['quantifier']
                  );
            }
         }
      }      
      return $this->_languages;
   }
 
   /**
    * Returns the primary language set in the Browser (converted to lowercase)
    *
    * @return string
    */  
   public function getPrimaryLanguage()
   {
      $langs = $this->getLanguages();
      return strtolower($langs[0]['primarytag']);
   }
   
   /**
    * Function to obtain whether a certain language is set in the user's browser as accepted
    *
    * @param string $langCode The language code of the language to look after
    *
    * @return bool Returns true if the user accepts the specified language, false otherwise
    */
   public function languageAccepted($langCode)
   {
      $langs = $this->getLanguages();
      foreach ($langs as $lang)
      {
         if (strtolower($lang['primarytag']) == strtolower($langCode))
            return true;
      }
      return false;
   }
}
