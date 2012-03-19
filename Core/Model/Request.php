<?php
/**
 * Request.php contains the implementation of global Request class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     $Revision-Id$ $Date$
 * @since       2012-02-12
 *
 */

/**
 * The Core Request Model
 *
 * @category   Konekt
 * @package    Framework
 */
class Konekt_Framework_Core_Model_Request
{   
   const GEOIP_DIR = 'GeoIP';
   
   /** @var Konekt_Framework_Core_Model_Browser */
   protected $_browser;
   
   /** @var array */
   protected $_ipCountryCache = array();
   
   protected $_geoIpDir;
 

   /**
    * Class Constructor; Also sets up the GeoIP directory path
    * 
    */
   function __construct()
   {
      $this->_geoIpDir = Konekt::app()->getLibDir() . DS . self::GEOIP_DIR;
   }


   /**
    * Returns the Visitor's browser Singleton
    *
    * @return Konekt_Framework_Core_Model_Browser
    */
   function getBrowser()
   {
      if (!$this->_browser)
      {
         $this->_browser = new Konekt_Framework_Core_Model_Browser();
      }
      return $this->_browser;
   }
   
   
   protected function _geoIpLookupCountry($ip)
   {
      include_once($this->_geoIpDir . DS . "geoip.inc");
      
      $gi = geoip_open($this->_geoIpDir . DS . "GeoIP.dat", GEOIP_STANDARD);
      $result = geoip_country_code_by_addr($gi, $ip);
      geoip_close($gi);
      
      return $result;
   }
   
   /**
    * Retrieves the Visitor's country by it's IP address based on MaxMind's GeoIP API
    *
    */
   function getCountry()
   {
      $ip = $this->ip();
      if (empty($this->_ipCountryCache["$ip"]))
      {
         $this->_ipCountryCache["$ip"] = $this->_geoIpLookupCountry($ip);
      }
      
      return $this->_ipCountryCache["$ip"];   
   }
   
   /**
    * Returns the root domain name url (eg. http://www.example.com) of the current request
    *
    * @return string
    */
   public function getDomainRootUrl()
   {
      $pageURL = 'http';
      if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
      }
      $pageURL .= "://" . $_SERVER["SERVER_NAME"];
      
      if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= ":".$_SERVER["SERVER_PORT"];
      }
      return $pageURL;
   }
   
   
   /**
    * Returns the full URL of the current request
    *
    * @return string
    */
   public function currentPageUrl()
   {
      return $this->getDomainRootUrl() . $_SERVER["REQUEST_URI"];    
   }
   
   /**
    * Returns the current request URI
    *
    * @return string
    */
   public function uri()
   {
      return $_SERVER["REQUEST_URI"];    
   }
   
   
   /**
    * Returns the IP Address the Request is coming from
    *
    * @return string The Visitor's IP address
    */   
   public function ip()
   {
      return $_SERVER["REMOTE_ADDR"];
   }
   
   
   /**
    * Omni function for fetching get and post request variables
    *
    * @param string $method The Request method to fetch values from
    * @param string $key         The name of the variable
    * @param mixed  $default     The default value to be returned if the array item is not found
    * @param bool   $acceptEmpty If set to false, then empty (but set) Request values will be replaced by $default.
    *    If true (default) then in case of empty but set vales will be returned.
    *
    * @return mixed Returns the value from the request or $default if not found
    */   
   protected function _getRequestVariable($method, $key, $default, $acceptEmpty)
   {
      switch ($method)
      {
         case 'get':
            $source =& $_GET;
         break;
         
         case 'post':
            $source =& $_POST;
         break;
         
         default:
            throw new Exception("Request Method ($method) unkown");
         break;
      }
      
      if (!isset($source["$key"])) {
         return $default;
      }
      
      if (empty($source["$key"])) {
         return $acceptEmpty ? $source["$key"] : $default;
      }
      else {
         return $source["$key"];
      }
   }
   
   
   /**
    * Returns the value of a GET variable
    *
    * @param string $key         The name of the GET variable
    * @param mixed  $default     The default value to be returned if the array item is not found
    * @param bool   $acceptEmpty If set to false, then empty (but set) GET values will be replaced by $default.
    *    If true (default) then in case of empty but set vales will be returned.
    *
    * @return mixed  Returns the value from the GET request or $default if not found
    */   
   public function get($key, $default = NULL, $acceptEmpty = true)
   {
      return $this->_getRequestVariable('get', $key, $default, $acceptEmpty);
   }
   
   
   /**
    * Returns the value of a POST variable
    *
    * @param string $key         The name of the POST variable
    * @param mixed  $default     The default value to be returned if the array item is not found
    * @param bool   $acceptEmpty If set to false, then empty (but set) POST values will be replaced by $default.
    *    If true (default) then in case of empty but set vales will be returned.
    *
    * @return mixed  Returns the value from the POST request or $default if not found
    */   
   public function post($key, $default = NULL, $acceptEmpty = true)
   {
      return $this->_getRequestVariable('post', $key, $default, $acceptEmpty);
   }
   
}
