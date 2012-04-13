<?php
/**
 * Short description for file
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     3
 * @since       2011-12-12
 *
 */


class Konekt_Framework_Core_Controller_Abstract
{
   /** @var Konekt_Framework_Core_Model_Response */
   protected $response;
   
   /** @var Konekt_Framework_Core_Model_Request */
   protected $request;
   
   
   public function __construct()
   {
      $this->response = Konekt::app()->getResponse();
      $this->request  = Konekt::app()->getRequest();
   }

   public function redirect($url)
   {
      header("location: $url");
   }

}
