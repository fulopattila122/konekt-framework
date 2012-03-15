<?php
/**
 * Visitor.php contains the implementation of the core Visistor class.
 *
 *
 * @package     Konekt
 * @subpackage  Core
 * @copyright   Copyright (c) 2011 - 2012 Attila Fülöp
 * @author      Attila Fülöp
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     $Revision-Id$ $Date$
 * @since       2011-12-12
 *
 */


/**
 * A class for getting information about the Visitor and his/her/its belongings
 *
 * @package     Konekt
 */
class Konekt_Core_Model_Visitor
{
   const GEOIP_DIR = 'GeoIP';
   
   /** @var Konekt_Core_Model_Browser */
   protected $_browser;
   
   /** @var array */
   protected $_ipCountryCache = array();
   
   protected $_geoIpDir;
   
   function __construct()
   {
      $this->_geoIpDir = Konekt::app()->getLibDir() . DS . self::GEOIP_DIR;
   }
   
   /**
    * Returns the Visitor's browser object
    *
    * @return Konekt_Core_Model_Browser
    */
   function getBrowser()
   {
      if (!$this->_browser)
      {
         $this->_browser = new Konekt_Core_Model_Browser();
      }
      return $this->_browser;
   }
   
   /**
    * Returns the Visitor's IP address
    *
    * @return string
    */
   function getIpAddress()
   {
      return $_SERVER['REMOTE_ADDR'];
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
    *
    */
   function getCountry()
   {
      $ip = $this->getIpAddress();
      if (empty($this->_ipCountryCache["$ip"]))
      {
         $this->_ipCountryCache["$ip"] = $this->_geoIpLookupCountry($ip);
      }
      
      return $this->_ipCountryCache["$ip"];   
   }


}
