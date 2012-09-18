<?php
/**
 * Response.php contains the implementation of core Response class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     9 2012-09-18
 * @since       2012-03-15
 *
 */


/**
 * The core Response Class
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Model_Response
{
   const DEFAULT_TEMPLATE_BASE = 'konekt_core_errordoc_%d.tpl';
   
   // [Informational 1xx]
	const HTTP_CONTINUE = 100;
	const HTTP_SWITCHING_PROTOCOLS = 101;
   
	// [Successful 2xx]
	const HTTP_OK = 200;
	const HTTP_CREATED = 201;
	const HTTP_ACCEPTED = 202;
	const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
	const HTTP_NO_CONTENT = 204;
	const HTTP_RESET_CONTENT = 205;
	const HTTP_PARTIAL_CONTENT = 206;

	// [Redirection 3xx]
	const HTTP_MULTIPLE_CHOICES = 300;
	const HTTP_MOVED_PERMANENTLY = 301;
	const HTTP_FOUND = 302;
	const HTTP_SEE_OTHER = 303;
	const HTTP_NOT_MODIFIED = 304;
	const HTTP_USE_PROXY = 305;
	const HTTP_UNUSED= 306;
	const HTTP_TEMPORARY_REDIRECT = 307;

	// [Client Error 4xx]
	const errorCodesBeginAt = 400;
	const HTTP_BAD_REQUEST = 400;
	const HTTP_UNAUTHORIZED  = 401;
	const HTTP_PAYMENT_REQUIRED = 402;
	const HTTP_FORBIDDEN = 403;
	const HTTP_NOT_FOUND = 404;
	const HTTP_METHOD_NOT_ALLOWED = 405;
	const HTTP_NOT_ACCEPTABLE = 406;
	const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
	const HTTP_REQUEST_TIMEOUT = 408;
	const HTTP_CONFLICT = 409;
	const HTTP_GONE = 410;
	const HTTP_LENGTH_REQUIRED = 411;
	const HTTP_PRECONDITION_FAILED = 412;
	const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
	const HTTP_REQUEST_URI_TOO_LONG = 414;
	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const HTTP_EXPECTATION_FAILED = 417;

	// [Server Error 5xx]
	const HTTP_INTERNAL_SERVER_ERROR = 500;
	const HTTP_NOT_IMPLEMENTED = 501;
	const HTTP_BAD_GATEWAY = 502;
	const HTTP_SERVICE_UNAVAILABLE = 503;
	const HTTP_GATEWAY_TIMEOUT = 504;
	const HTTP_VERSION_NOT_SUPPORTED = 505;
   
   /**
    * Status texts of all known HTTP response codes
    *
    * @var array
    */
   protected static $_statusMessages = array(
      // Informational 1xx
      100 => 'Continue',
      101 => 'Switching Protocols',

      // Success 2xx
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',

      // Redirection 3xx
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',  // 1.1
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      // 306 is deprecated but reserved
      307 => 'Temporary Redirect',

      // Client Error 4xx
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',

      // Server Error 5xx
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      509 => 'Bandwidth Limit Exceeded'
   );
   
   /**
    * The global Smarty Instance
    * 
    * @var Smarty
    */
   protected $_smarty;

   /**
    * The internal status code placeholder
    * 
    * @var int 
    */
   private $_statusCode = 200;
   
   /**
    * The Custom Message to display in errors instead of the original
    * 
    * @var string
    */
   private $_customMessage = null;
   
   /**
    * The Redirect to URL
    * 
    * @var string
    */
   private $_redirectToUrl = null;
   
   /**
    * The Assigned Template's name
    * 
    * @var string
    */
   private $_template = '';
   
   /**
    * The Template prefix name
    * 
    * @var string
    */
   private $_templatePrefix = '';
   
   
   /**
    * Class Constructor; Invokes Smarty initialization
    * 
    */
   function __construct()
   {
      $this->_setupSmarty();
      $prefix = Konekt::app()->getConfigValue('core/templatePrefix');
      $this->_templatePrefix = empty($prefix) ? '' : (string) $prefix;
   }
   
   
   /**
    * Sets up the Smarty directories
    * 
    * @param Smarty  $smarty  The smarty instance to set up
    */
   private function _setupSmartyDirectories($smarty)
   {
      $smarty->compile_dir = Konekt::app()->getVarDir() . '/smarty/templates_c';
      $smarty->cache_dir   = Konekt::app()->getVarDir() . '/smarty/cache';
      $smarty->config_dir  = Konekt::app()->getVarDir() . '/smarty/configs';
   }
   
   
   /**
    * Initializes the global Smarty instance
    *
    */
   private function _setupSmarty()
   {
      require_once (Konekt::app()->getLibDir($this) . DS . 'Smarty' . DS . 'Smarty.class.php');
      
      $this->_smarty = new Smarty();
      $this->_setupSmartyDirectories($this->_smarty);
   }
   
   
   /**
    * Sends the rendered smarty template to the output
    *
    * @return bool Returns true on success
    */   
   protected function _displaySmarty()
   {
      $this->_smarty->display($this->_templatePrefix . $this->_template);
      return true;
   }
   
   /**
    * Sends an Error Page to the output
    * 
    * First it looks for an errordoc template in the config file in
    * the core/errordocs/<error_code> node. If not set falls back to
    * <errorcode>.tpl. If none of these templates exist, it falls back
    * to the built in konekt_core_errordoc_<errorcode>.tpl template.
    * 
    * If that fails to, displays a tiny Houston we have a problem here
    * error message. This case however shouldn't happen.
    * 
    * @return bool Returns true on success
    */
   protected function _displayError()
   {
      $tpl = Konekt::app()->getConfigValue('core/errordocs/' . $this->_statusCode);
      
      $tpl || $tpl = $this->_statusCode . '.tpl';
      $this->_smarty->templateExists($tpl) || $tpl = sprintf(self::DEFAULT_TEMPLATE_BASE, $this->_statusCode);
      
      header($_SERVER['SERVER_PROTOCOL'] . ' ' .$this->_statusCode . ' '
             . self::$_statusMessages[$this->_statusCode], true, $this->_statusCode);
      if ($this->_smarty->templateExists($tpl))
      {
         $message = $this->_customMessage ? $this->_customMessage : Konekt::app()->getRequest()->uri();
         $this->setAttribute('message', $message);
         $this->_smarty->display($tpl);
      }
      else
      {
         echo "<h1>Houston</h1>\n<p>We have a problem here</p>\n";
      }
      
      return true;
   }
   
   /**
    * Sends the a redirect to header to the previously set redirect URI
    * 
    * @return bool Returns true
    */
   protected function _sendRedirect()
   {
      header("Location: " . $this->_redirectToUrl);
      return true;
   }
   
   
   /**
    * Adds a directory to the list of template directories
    *
    * @param string $dir The directory to be added to the list of Template Folders
    *
    * @return
    */
   public function addTemplateDir($dir)
   {
      $this->_smarty->addTemplateDir($dir);
   }
   
   /**
    * Sets the template
    *
    * @param string $template The Template to be rendered
    */   
   public function setTemplate($template)
   {
      $this->_template = $template;
   }
   
   
   /**
    * Set the template prefix. This is useful for applications willing to override default design by
    * creating a new set of template files with the same name, but prefixed.
    * 
    * @param   string   $prefix  The prefix string to be added to the template
    * 
    */
   public function setTemplatePrefix($prefix)
   {
      $this->_templatePrefix = $prefix;
   }
   

   /**
    * Sets an output attribute (typically a template variable)
    *
    * @param string $name  The name of the attribute/variable
    * @param mixed  $value The value to be assigned
    */
   public function setAttribute($name, $value)
   {
      $this->_smarty->assign($name, $value);
   }
   
   
   /**
    * Displays (sends) the Rendered Output (Webpage) to the visitor
    *
    */   
   public function display()
   {
      switch ($this->_statusCode)
      {
         case self::HTTP_OK:
            $this->_displaySmarty();
            break;
            
         case self::HTTP_FOUND:
            $this->_sendRedirect();
            break;
            
         case self::HTTP_NOT_FOUND:
         case self::HTTP_INTERNAL_SERVER_ERROR:
            $this->_displayError();
            break;
            
         default:
            $this->_displayNotImplemented();
      }

   }
   
   
   /**
    * Sets the response to redirect
    *
    * @param string $url   The URL to redirect to
    */   
   public function redirect($url)
   {
      $this->_statusCode     = self::HTTP_FOUND;
      $this->_redirectToUrl  = $url;
      
      return $this;
   }
   
   
   /**
    * Sets the well known 404 Not Found error
    *
    * @param string $customUri   The URI to display instead of the original request
    */   
   public function err404($customUri = null)
   {
      $this->_statusCode                  = self::HTTP_NOT_FOUND;
      $customUri && $this->_customMessage = $customUri;
      
      return $this;
   }
   
   /**
    * Sets the shameful 500 Internal Server error
    *
    * @param string $message   The pity explanation on what's happening
    */   
   public function err500($message = null)
   {
      $this->_statusCode                  = self::HTTP_INTERNAL_SERVER_ERROR;
      $message && $this->_customMessage = $message;
      
      return $this;
   }
   
   /**
    * Retruns the Smarty Object initiated for the Response
    * 
    * @return  Smarty
    */
    public function getSmarty()
    {
       return $this->_smarty;
    }
    
    /**
    * Retruns a new Smarty Instance (independent from the one for response output)
    * 
    * This should be used for getting individual output from templates, eg. E-mail templates.
    * 
    * @param   bool  $cloneTemplateDirs   If true, all the template directories (set up by modules)
    *                                     of the main smarty instance will be added to the newly
    *                                     created instance
    * 
    * @return  Smarty
    */
    public function getNewSmartyInstance($cloneTemplateDirs = true)
    {
       $result = new Smarty();
       $this->_setupSmartyDirectories($result);
       
       if ($cloneTemplateDirs) {
          $result->setTemplateDir($this->_smarty->getTemplateDir());
       }
       return $result;
    }
    
   
   /**
    * Returns the Response's HTTP status code
    *
    * @return  int   The response's status code
    */
   public function getStatusCode()
   {
      return $this->_statusCode;
   }
   
}
