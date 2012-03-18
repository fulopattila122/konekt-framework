<?php
/**
 * Router.php contains the implementation of the core Router class
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
 * The Konekt core router class
 *
 * @category   Konekt
 * @package    Framework
 */
class Konekt_Framework_Core_Model_Router
{
   /**
    * Routing variable types
    *
    * @var array
    */
   protected static $_varTypes = array(
      'any'    => '.+',
      'num'    => '[\d]+',
      'slug'   => '[a-zA-Z0-9_-]+',
      'alpha'  => '[a-zA-Z]+'
   );
   
   /**
    * The normalized request URI
    * 
    * @var string
    */
   protected $_uri;
   
   /**
    * The Prepared Routing Table
    * 
    * @var array
    * @see _prepare
    */
   protected $_routingTable;
   
   /**
    * The resolved controller instance
    * 
    * @var Konekt_Framework_Core_Controller_Abstract
    */
   protected $_controller;
   
   /**
    * Removes Garbage from the Request URI
    *
    * @param string $source The source file the request is coming from
    */
   protected function _normalize($source)
   {
      $this->_uri = Konekt::app()->getRequest()->uri();
      $p = strpos($this->_uri, $source . '/');
      
      if ($p === 1) {
         $this->_uri = substr($this->_uri, $p + strlen($source));
      }
   }
   
   /**
    * Parses and replaces one variable at a given position
    * 
    * @param string $str   The route pattern as set in the config
    * @param int    $pos   The position in the string where the variable has to be replaced
    * @param string $var   The found variable (type)
    * @param string $repl  The replacement value
    * 
    * @return string The converted regexp route pattern
    */
   private function _replaceVariable($str, $pos, $var, $repl)
   {
      $expr   = substr($str, $pos, strpos($str, ')', $pos + 1) - $pos + 1);
      $eqsign = strpos($expr, '=');
      if (false !== $eqsign)
      {
         $name = substr($expr, $eqsign + 1, strlen($expr) - $eqsign - 2);
      }
      return substr_replace($str, "(?P<$name>$repl)", $pos, strlen($expr));
   }
   
   /**
    * Replaces variables to proper regular expressions
    * 
    * @param string $route  The route pattern as set in the config
    * 
    * @return string The converted regexp route pattern
    */
   private function _replaceVariables($route)
   {
      $result = $route;
      foreach (self::$_varTypes as $var => $replacement)
      {
         while ($p = strpos($result, "(.$var"))
         {
            $result = $this->_replaceVariable($result, $p, $var, $replacement);
         }
      }
      
      return $result;
   }
   
   
   /**
    * Prepares the routing table. Loads from the config and converts
    * nice wildcards to valid regexes.
    * 
    */
   protected function _prepare()
   {
      $rt = Konekt::app()->getConfigValue('routing');
      foreach ($rt as $route => $settings)
      {
         if (is_array($settings))
         {
            $ctrl   = $settings['controller'];
            $params = isset($settings['params']) ? $settings['params'] : array();
         }
         else
         {
            $ctrl   = $settings;
            $params = array();
         }
         
         
         $this->_routingTable[$this->_replaceVariables($route)] = array(
            'controller'   => $ctrl,
            'params'       => $params
            );
      }
   }
   
   /**
    * Looks for a route matching the request
    *
    * @return string|bool  Returns the matching route or false if none found
    */
   protected function _findRoute()
   {
      $result = false;
      
      if ('/' == $this->_uri || '' == $this->_uri)
      {
         $result = $this->_routingTable['/'];
      }
      else
      {
         foreach ($this->_routingTable as $route => $settings)
         {
            if (preg_match(":^/?$route/?$:", $this->_uri, $a)) {
               $result = array();
               $result['controller'] = $settings['controller'];
               $result['params']     = array_merge($settings['params'], $a);
               break;
            }
         }
      }
      return $result;
   }
   
   /**
    * Reutrns the controller class name based on it's router notation
    * 
    * @param string $name The routing name
    * 
    * @return string The class name of the Controller
    */
   private function _getControllerClassName($name)
   {
      $parts  = explode('/', $name);
      $result = '';
      
      //Vendor part
      foreach ( explode('_', $parts[0]) as $key )
      {
         $result .= ucfirst($key) . '_';
      }
      //Package part
      foreach ( explode('_', $parts[1]) as $key )
      {
         $result .= ucfirst($key) . '_';
      }
      //Module part
      foreach ( explode('_', $parts[2]) as $key )
      {
         $result .= ucfirst($key) . '_';
      }
      
      $result .= 'Controller';
      
      if (empty($parts[3]))
      {
         $result .= '_Default';
      }
      else foreach ( explode('_', $parts[3]) as $key )
      {
         $result .= '_'.ucfirst($key);
      }
      return $result;
   }
   
   
   /**
    * Returns the controller class based on the resolved route string
    *
    * @param string $route The resolved route string
    *
    * @return Konekt_Framework_Core_Controller_Abstract|bool Returns a Controller class instance, or false
    */
   protected function _getController($route)
   {
      $ctrlClass = $this->_getControllerClassName($route);
      $this->_controller = new $ctrlClass;
      
      if (!is_subclass_of($this->_controller, 'Konekt_Framework_Core_Controller_Abstract')) {
         return false;
      }
      
      return $this->_controller;
   }
   
   
   /**
    * Returns the action method based on the resolved route string
    *
    * @param string $route The resolved route string
    *
    * @return string|bool The action method name, false on failure
    */
   protected function _getAction($route)
   {
      $parts   =  explode('/', $route);
      $result  =  isset($parts[4]) ? $parts[4] : 'index';
      $result .=  'Action';
      
      return method_exists($this->_controller, $result) ? $result : false;      
   }
   
   
   /**
    * Routes an incoming request to the appropriate controller based on the routing configuration
    *
    * @param string $source The source file the dispatch request is coming from
    *
    * @return Konekt_Core_Model_Response Returns the response object
    */
   public function dispatch($source)
   {
      $this->_normalize($source);
      $this->_prepare();
      
      $route = $this->_findRoute();
      
      $response = Konekt::app()->getResponse();
      
      if (false === $route) {
         return $response->err404();
      }
      
      $controller = $this->_getController($route['controller']);

      if (!$controller) {
         return $response->err404();
      }
      
      $action = $this->_getAction($route['controller']);
      
      if (!$action) {
         return $response->err404();
      }
      
      $controller->$action($route['params']);
      
      return $response;
   }
   
}
