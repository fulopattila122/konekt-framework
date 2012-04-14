<?php
/**
 * Interface.php contains the declaration of interface Konekt_Auth_Model_Interface
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Auth
 * @copyright   Copyright (c) 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     2 2012-04-14
 * @since       2012-03-15
 *
 */



/**
 * Interface for all Authentication classes
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Auth
 */
interface Konekt_Framework_Auth_Model_Interface
{
   /**
    * Attempts login with the supplied credentials
    *
    * @param   array $credentials The login credentials
    *
    * @return  bool  Returns true if successfully logged in, false otherwise
    */
   public function login($credentials);
   
   
   /**
    * Logs out the currently logged in user
    *
    * @return  bool  Returns true on successful logout
    */
   public function logout();
   
   
   /**
    * Returns logged in status
    *
    * @return  bool  Returns true if there's a user logged in, false otherwise
    */
   public function isLoggedIn();
   
   
   /**
    * Returns the currently logged in User's id
    *
    * @return  mixed The return value depends on the underlying driver. NULL if the user isn't logged in
    */
   public function getUserId();
   
   
   /**
    * Returns the currently logged in User's group membership, if the driver supports it
    *
    * @return  string   The group(s) of the logged in user. If there are multiple groups, they should be comma separated. Drivers not supporting groups should return an empty string
    */
   public function getGroup();
   

   /**
    * Returns the ACL list of the currently logged in user
    *
    * @return  array The simple array of ACs enabled
    */   
   public function getAcl();
   

   /**
    * Returns the Currently logged in user's screen name
    *
    * @return  string   The Publicly Visible name of the user
    */   
   public function getScreenName();
   
   
   /**
    * Obtains whether or not the current user is member of a given group
    *
    * @param   string $group   The name of the group (case insensitive)
    *
    * @return  bool  Returns true if the current user is member of the current group, false if not  
    */   
   public function memberOf($group);
   
   
   /**
    * Verifies if the user has access to the provided Acess Control
    *
    * @param   mixed $ac   The Access Control condition (name, id, etc)
    *
    * @return  bool  Returns true if the current user has access to the given AC, false if not
    */
   public function hasAccessTo($ac);
   
}
