<?php
/**
 * Observer.php contains the implementation of the Doctrine Connection Observer (Listener) class
 *
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 * @copyright   Copyright (c) 2011 - 2012 Attila Fulop
 * @author      Attila Fulop
 * @license     GNU LGPL v3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version     1 2012-07-13
 * @since       2012-07-13
 *
 */


/**
 * The Doctrine Connection Observer (Listener in Doctrine terminology) Class
 * 
 * It defines some observers for the global doctrine connection events
 *
 * @category    Konekt
 * @package     Framework
 * @subpackage  Core
 */
class Konekt_Framework_Core_Model_Connection_Observer implements Doctrine_EventListener_Interface
{
    
   public function preTransactionCommit( Doctrine_Event $event ) {}
   public function postTransactionCommit( Doctrine_Event $event ) {}

   public function preTransactionRollback( Doctrine_Event $event ) {}
   public function postTransactionRollback( Doctrine_Event $event ) {}

   public function preTransactionBegin( Doctrine_Event $event ) {}
   public function postTransactionBegin( Doctrine_Event $event ) {}

   /**
    * Automatically sets the charset to utf8
    *
    */
   public function postConnect( Doctrine_Event $event )
   {
      $invoker = $event->getInvoker();
      
      /* If Invoker is not a connection then it's either a
         Doctrine_Connection_Statement or a Doctrine_Connection_UnitOfWork
         or a Doctrine_Transaction instance. These all have a getConnection
         method for retrieving the connection instance
      */
      $connection = is_subclass_of($invoker, 'Doctrine_Connection') ? $invoker : $invoker->getConnection();
      $connection->setCharset("utf8");
   }
   
   public function preConnect( Doctrine_Event $event ) {}

   public function preQuery( Doctrine_Event $event ) {}
   public function postQuery( Doctrine_Event $event ) {}

   public function prePrepare( Doctrine_Event $event ) {}
   public function postPrepare( Doctrine_Event $event ) {}

   public function preExec( Doctrine_Event $event ) {}
   public function postExec( Doctrine_Event $event ) {}

   public function preError( Doctrine_Event $event ) {}
   public function postError( Doctrine_Event $event ) {}

   public function preFetch( Doctrine_Event $event ) {}
   public function postFetch( Doctrine_Event $event ) {}

   public function preFetchAll( Doctrine_Event $event ) {}
   public function postFetchAll( Doctrine_Event $event ) {}

   public function preStmtExecute( Doctrine_Event $event ) {}
   public function postStmtExecute( Doctrine_Event $event ) {}
}
