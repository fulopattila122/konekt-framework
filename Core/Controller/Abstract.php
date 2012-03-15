<?php


class Konekt_Framework_Core_Controller_Abstract
{

   public function redirect($url)
   {
      header("location: $url");
   }

}
