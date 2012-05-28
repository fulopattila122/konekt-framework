<?php
/**
 * Email.php contains the Email Model Class which is a wrapper around PHPMailer
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Email
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     2 2012-05-28
 * @since       2012-05-12
 *
 */


require_once('class.phpmailer.php');

/**
 * The Email model class for composing and sending emails
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Email
 */
class Konekt_Framework_Email_Model_Email {
   
   const SEND_TYPE_MAIL       = 'mail';
   const SEND_TYPE_SENDMAIL   = 'sendmail';
   const SEND_TYPE_QMAIL      = 'qmail';
   const SEND_TYPE_SMTP       = 'smtp';
   
   const DEFAULT_HOST         = 'localhost';
   const DEFAULT_TYPE         = 'mail';
   const DEFAULT_PORT         = 25;
   const DEFAULT_AUTH         = false;
   
   /** @var Konekt_Framework_Core_Model_Config */
   protected $_config;
   
   /** @var PHPMailer */
   protected $_mailer;
   
   /**
    * Class Constructor;
    * 
    */
   function __construct()
   {
      $this->_config = Konekt::app()->getConfig()->getValue('email', array());
      $this->_mailer = new PHPMailer();
      $this->_init();
   }
   
   
   /**
    * Initializes the Sender from the local configuration
    *
    * @return boolean
    */
   protected function _initFrom()
   {
      $email = !isset($this->_config['from']['email']) || empty($this->_config['from']['email']) ? '' : $this->_config['from']['email'];
      $name  = !isset($this->_config['from']['name'])  || empty($this->_config['from']['name'])  ? '' : $this->_config['from']['name'];
      return $this->_mailer->SetFrom($email, $name);
   }
   
   /**
    * Initializes the Email from the configuration
    *
    */
   protected function _init()
   {
      $this->_mailer->CharSet = "utf-8";
      
      $this->_initFrom();
      switch ($this->_config['type'])
      {
         case self::SEND_TYPE_MAIL:
            $this->_mailer->IsMail();
            break;
         
         case self::SEND_TYPE_SENDMAIL:
            $this->_mailer->IsSendmail();
            break;
         
         case self::SEND_TYPE_QMAIL:
            $this->_mailer->IsQmail();
            break;
         
         case self::SEND_TYPE_SMTP:
            $this->_mailer->IsSMTP();
            $this->_mailer->Host = (!isset($this->_config['conf']['host']) || empty($this->_config['conf']['host'])) ? self::DEFAULT_HOST : $this->_config['conf']['host'];
            $this->_mailer->Port = !isset($this->_config['conf']['port']) || empty($this->_config['conf']['port']) ? self::DEFAULT_PORT : (int)$this->_config['conf']['port'];
            
            if (isset($this->_config['conf']['secure']) )
            {
               if ('ssl' === strtolower($this->_config['conf']['secure']) || 'tls' === strtolower($this->_config['conf']['secure']) )
               {
                  $this->_mailer->SMTPSecure = strtolower($this->_config['conf']['secure']);
               }
            }
            $this->_mailer->SMTPAuth = !isset($this->_config['conf']['auth']) || empty($this->_config['conf']['auth']) ? self::DEFAULT_AUTH : (bool)$this->_config['conf']['auth'];

            if ($this->_mailer->SMTPAuth)
            {
               $this->_mailer->Username  = $this->_config['conf']['username'];
               $this->_mailer->Password  = Konekt::helper('core')->decrypt($this->_config['conf']['password']);
            }
            break;
         
         default:
            throw new Exception('Unknown E-mail sending method: ' . $this->_config['type']);
            break;
      }
   }
   
   /**
    * Adds a "To" address.
    *
    * @param string $email
    * @param string $name
    *
    * @return boolean true on success, false if address already used
    */
   public function addAddress($email, $name = '')
   {
      return $this->_mailer->AddAddress($email, $name);
   }
   
   
   /**
    * Adds an attachment from a path on the filesystem.
    *
    * @param string $path     Path to the attachment.
    * @param string $name     Overrides the attachment name.
    * @param string $encoding File encoding (see $Encoding).
    * @param string $type     File extension (MIME) type.
    *
    * @return bool            Return true on success, false if the file could not be found or accessed.
    */
   public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
   {
      return $this->_mailer->AddAttachment($path, $name, $encoding, $type);
   }
   
   
   /**
    * Adds a "BCC" address.
    *
    * @param string $email
    * @param string $name
    *
    * @return boolean true on success, false if address already used
    */
   public function addBCC($email, $name = '')
   {
      return $this->_mailer->AddBCC($email, $name);
   }
   
   
   /**
    * Adds a "CC" address.
    *
    * @param string $email
    * @param string $name
    *
    * @return boolean true on success, false if address already used
    */
   public function addCC($email, $name = '')
   {
      return $this->_mailer->AddCC($email, $name);
   }
   
   
   /**
    * Adds a custom E-mail header
    * 
    * @param string $header  The header in 'Headername: Headervalue' format
    *
    * @return void
    */
   public function addCustomHeader($header)
   {
      $this->_mailer->AddCustomHeader($header);
   }
   
   
   /**
    * Adds an embedded attachment. This can include images, sounds, and
    * just about any other document. For image or media types other than jpg, gif, png
    * make sure to set the $type to it's appropriate type. Type for jpg, gif & png are
    * set automatically.
    * 
    * For JPEG images use "image/jpeg" and for GIF images
    * use "image/gif".
    * 
    * @param string $path     Path to the attachment.
    * @param string $cid      Content ID of the attachment. Use this to identify
    *                         the Id for accessing the image within HTML.
    * @param string $name     Overrides the attachment name.
    * @param string $encoding File encoding ("8bit", "7bit", "binary", "base64", or "quoted-printable").
    * @param string $type     File extension (MIME) type (For unkown types application/octet-stream will be used).
    * 
    * @return bool
    */
   public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64',
                                    $type = null)
   {
      //Tries obtains MIME type unless it's provided
      $type = (null !== $type) ? Konekt::helper('core/image')->getMimeType($path) : $type;
      
      return $this->_mailer->AddEmbeddedImage($path, $cid, $name, $encoding, $type);
   }
   
   
   /**
    * Adds an embedded attachment. This can include images, sounds, and
    * just about any other document. For image or media types other than jpg, gif, png
    * make sure to set the $type to it's appropriate type. Type for jpg, gif & png are
    * set automatically.
    * 
    * For JPEG images use "image/jpeg" and for GIF images
    * use "image/gif".
    * 
    * @param string $buffer   The buffer containing the attachment.
    * @param string $cid      Content ID of the attachment. Use this to identify
    *                         the Id for accessing the image within HTML.
    * @param string $name     Overrides the attachment name.
    * @param string $encoding File encoding ("8bit", "7bit", "binary", "base64", or "quoted-printable").
    * @param string $type     File extension (MIME) type (For unkown types application/octet-stream will be used).
    * 
    * @return void
    */
   public function addStringEmbeddedImage($buffer, $cid, $name = '', $encoding = 'base64',
                                    $type = null)
   {
      if (null === $type)
      {
         $extension = Konekt::helper('core/image')->getImageType($buffer);
         //In case of extension couldn't be obtained, the getMimeType will fall back to application/octet-stream
         $type = Konekt::helper('core/image')->getMimeType($filename . $extension);   
      }
      
      $this->_mailer->AddStringEmbeddedImage($buffer, $cid, $name, $encoding, $type);
   }
   
   
   /**
    * Adds an attachment from a path on the filesystem.
    *
    * @param string $buffer   The buffer containing the attachment.
    * @param string $name     Overrides the attachment name.
    * @param string $encoding File encoding (see $Encoding).
    * @param string $type     File extension (MIME) type.
    *
    * @return void
    */
   public function addStringAttachment($buffer, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
   {
      $this->_mailer->AddStringAttachment($buffer, $name, $encoding, $type);
   }
   
   
   /**
    * Adds a "Reply-To" address.
    *
    * @param string $email
    * @param string $name
    *
    * @return boolean
    */
   public function addReplyTo($email, $name = '')
   {
      return $this->_mailer->AddReplyTo($email, $name);
   }
   
   
   /**
    * Enables The SMTP Debug functionality
    *
    * @return void;
    */
   public function enableSmtpDebug()
   {
      $this->_mailer->SMTPDebug = true;
   }
   
   
   /**
    * Returns the PHPMailer Error info
    *
    * @return  string
    */
   public function getErrorInfo()
   {
      return $this->_mailer->Host . ", " . (string)$this->_mailer->Port . ", " . $this->_config['conf']['port'] . "\n" . $this->_mailer->ErrorInfo;
   }
   
   
   /**
    * Sends out the Email (using to the local configuration).
    * 
    * @return bool
    */
   public function send()
   {
      return $this->_mailer->Send();
   }
   
   
   /**
    * Sets the From and FromName properties
    * 
    * @param string $email
    * @param string $name
    *
    * @return boolean
    */
   public function setFrom($email, $name)
   {
      return $this->_mailer->SetFrom($email, $name);
   }
   
   
   /**
    * Sets the E-mail message body if the content to be sent is both html and plain text.
    *
    * 
    * @param string $html  The html version of the message
    * @param string $text  The plain text version of the message
    *
    * @return void
    */
   public function setHtmlAndTextBody($html, $text = '')
   {
      $this->_mailer->MsgHTML($html);
      if (!empty($text)) {
         $this->_mailer->AltBody = $text;
      }
   }
   
   
   /**
    * Sets the E-mail message body if the content to be sent is plain text only.
    *
    * Note that if you want to send out an E-mail with both html and txt version
    * included (aka. multipart/alternative) then you must use the setHtmlAndTextBody
    * method. This method is for plain text only messages.
    *
    * @see  setHtmlAndTextBody
    * 
    * @param string $message  The message body in plain text format
    *
    * @return boolean
    */
   public function setPlaintextOnlyBody($message)
   {
      $this->_mailer->Body = $message;
      $this->_mailer->IsHTML(false);
   }
   
   /**
    * Sets the E-mail Subject
    * 
    * @param string $subject
    */
   public function setSubject($subject)
   {
      $this->_mailer->Subject = $subject;
   }
   
}
