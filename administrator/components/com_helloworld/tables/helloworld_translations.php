<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class HelloWorldTableHelloWorld_translations extends JTable {
  
  /**
   * 
   * 
   * Woot! Access the HelloWorld_translations table, baby
   * Since, I wrote it.
   */

  var $id = '';
  var $property_id = '';
  var $lang_code = '';
  var $greeting = '';
  var $description = '';

  function __construct(&$db) {
    parent::__construct('#__helloworld_translations', 'id', $db);
  }

}