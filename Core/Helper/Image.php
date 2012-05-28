<?php
/**
 * Image.php contains the implementation of core Image Helper class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-05-28
 * @since       2012-05-28
 *
 */


/**
 * The class contains utilities for processing image files
 *
 * @caegory    Konekt
 * @package    Framework
 * @subpackage Core
 */
class Konekt_Framework_Core_Helper_Image extends Konekt_Framework_Core_Helper_Abstract{


   /**
    * Returns the Image width & height
    *
    * @param string $file The image file name
    *
    * @return array In the format array( w => <width>, h => <height> )
    */
   public function getImageSize($file)
   {
      $result = array('w' => 0, 'h' => 0);
      $buf = file_get_contents($file);
      if ($buf === false)
         return $result;
      if (!function_exists("imagecreatefromstring"))
         return $result;

      $image = imagecreatefromstring($buf);
      if (!$image)
         return $result;

      $result['w'] = imagesx($image);
      $result['h'] = imagesy($image);
      imagedestroy($image);
      
      return $result;
   }
   
   
   /**
    * Obtains the Image File type based on its first several bytes
    *
    * @param array|string $buffer The file data's first 32 bytes (at least)
    *
    * @return string The Image file type's extension (bmp, gif, jpg, png)
    */
   public function getImageType($buffer)
   {
   
	   if(strlen($buffer)>3 &&  ord($buffer[0])==0xff && ord($buffer[1])==0xd8 &&
	         ord($buffer[2])==0xff)
		   return "jpg";
		   
	   if(strlen($buffer)>8 &&  ord($buffer[0])==0x89 && ord($buffer[1])==0x50 &&
	         ord($buffer[2])==0x4e && ord($buffer[3])==0x47 && ord($buffer[4])==0x0d &&
	         ord($buffer[5])==0x0a && ord($buffer[6])==0x1a && ord($buffer[7])==0x0a)
		   return "png";
		   
	   if(strlen($buffer)>2 &&  $buffer[0]=='G' && $buffer[1]=='I' && $buffer[2]=='F')
		   return "gif";
		   
	   if(strlen($buffer)>1 && $buffer[0]=='B' && $buffer[1]=='M')
		   return "bmp";
		   
   }
   
   
   /**
    * Obtains and returns the image file's MIME type name based on the file extension
    *
    * @param string  $filename
    *
    * @return string Retruns 'image/<type>' for known types or 'application/octet-stream' for unknown ones.
    */
   public function getMimeType($filename)
   {
      switch (Konekt::helper('core')->getFileExt($filename, true))
      {
         case 'jpg':
         case 'jpeg':
         case 'jpe':
         case 'jfif':
            $type = 'jpeg';
            break;
         
         case 'png':
            $type = 'png';
            break;
         
         case 'gif':
            $type = 'gif';
            break;
         
         case 'tif':
         case 'tiff':
            $type = 'image/tiff';
            break;
         
         case 'bm':
         case 'bmp':
            $type = 'x-windows-bmp';
            break;
         
         case 'ico':
            $type = 'x-icon';
            break;
         
         case 'pbm':
            $type = 'x-portable-bitmap';
            break;
         
         default:
            return 'application/octet-stream';
            break;
      }
      
      return "image/$type";
   }
   
   
}
