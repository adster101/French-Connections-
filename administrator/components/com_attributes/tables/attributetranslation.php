<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class AttributesTableAttributeTranslation extends JTable {
  
  /**
   * 
   * 
   * Woot! Access the HelloWorld_translations table, baby
   * Since, I wrote it.
   */

  function __construct(&$db) {
    parent::__construct('#__attributes_translation', 'id', $db);
  }

}