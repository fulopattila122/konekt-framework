<?php
/**
 * Email.php contains the Core Email Helper Class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-05-12
 * @since       2012-05-12
 *
 */


require_once('Mail.php');

/**
 * The Email helper class containing utilities for dealing with emails
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Helper_Email extends Konekt_Framework_Core_Helper_Abstract{
   
   const DEFAULT_HOST   = 'localhost';
   const DEFAULT_TYPE   = 'sendmail';
   const DEFAULT_PORT   = 25;
   const DEFAULT_AUTH   = false;
   
   /** @var Konekt_Framework_Core_Model_Config */
   protected $_config;
   
   /**
    * Class Constructor;
    * 
    */
   function __construct()
   {
      $this->_config = Konekt::app()->getConfig();
      parent::__construct();
   }
   
   /**
    * Retrieves the E-mail configuration
    * 
    * @return array
    */
   private function _getEmailConf()
   {
      $result = $this->_config->getValue('core/email/conf');
      
      if (empty($result['host'])) $result['host'] = DEFAULT_HOST;
      if (empty($result['auth'])) $result['auth'] = DEFAULT_AUTH;
      if (empty($result['port'])) $result['port'] = DEFAULT_PORT;
      
      return $result;
   }
   
   /**
    * Sends out an Email according to the local configuration.
    * 
    * This method is actually a wrapper of the pear Mail functionality.
    *
    * @param string|array $to_email The E-mail address(es) to send the E-mail to
    *                               if string is passed the To: field will be formatted:
    *                               $to_name <$to_email>. If array, then the array elements will
    *                               be used, separated by commas (,)
    * @param string  $to_name       Only used if $to_email is a string
    * @param string  $subject       The Subject of the E-mail
    * @param string  $sender_name   The Name of the sender on the E-mail envelope. If empty string is
    *                               passed, then the core/email/from/name config value will be used.
    * @param string  $sender_email  The E-mail of the sender on the E-mail envelope. If empty string is
    *                               passed, then the core/email/from/email config value will be used.
    *
    * @return bool|string Returns true on success, the error message string on failure
    */
   public function SendEmail($to_email, $to_name, $subject, $body, $sender_name = '', $sender_email = '')
   {
      $email_conf = $this->_getEmailConf();
      
      if (is_array($to_email)){
         $to = implode(",", $to_email);
      } else {
         $to = "=?utf-8?b?" . base64_encode($to_name) . "?= <$to_email>";
      }
      
      //Obtain Sender envelope
      $outgoing_email_conf = $this->_config->getValue('core/email/from');
      $sname  = empty($sender_name)  ? $outgoing_email_conf['name']  : $sender_name;
      $semail = empty($sender_email) ? $outgoing_email_conf['email'] : $sender_email;
      
      $headers = array('From'       => "=?utf-8?b?" . base64_encode($sname) . "?= <$semail>",
                        'To'        => $to,
                        'Subject'   => "=?utf-8?b?" . base64_encode($subject) . "?="
                        );
      
      $mail =& Mail::factory($this->_config->getValue('core/email/type'), $email_conf);

      $result = $mail->send($to, $headers, wordwrap($body, 70));
      
      if ( PEAR::isError($result))
      {
         return $result->getMessage();
      }
      else
      {
         return true;
      }
   }
   
   
}
