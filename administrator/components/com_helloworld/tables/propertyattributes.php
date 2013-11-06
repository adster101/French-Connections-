<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class HelloWorldTablePropertyAttributes extends JTable {

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db) {
    parent::__construct('#__unit_attributes', 'property_id', $db);
  }

  /**
   * Overloaded save function
   * Takes the facilities and saves them into the availability table.
   *
   *
   */
  public function save($id = null, $attributes = array(), $old_version_id = '', $new_version_id = '') {

    if (!$this->check()) {

      //JLog::add('JDatabaseMySQL::queryBatch() is deprecated.', JLog::WARNING, 'deprecated');
      return false;
    } else {

      // Firstly need to delete these...in a transaction would be better
      $query = $this->_db->getQuery(true);

      if ($old_version_id == $new_version_id) {

        $query->delete('#__unit_attributes')->where('version_id = ' . $old_version_id);
        $this->_db->setQuery($query);

        if (!$this->_db->execute()) {

          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));

          $this->setError($e);
          return false;
        }
      }



      $query = $this->_db->getQuery(true);

      $query->insert('#__unit_attributes');

      $query->columns(array('version_id', 'property_id', 'attribute_id'));

      foreach ($attributes as $attribute) {
        $insert_string = "$new_version_id, $id," . $attribute . "";
        $query->values($insert_string);
      }

      $this->_db->setQuery($query);

      if (!$this->_db->execute()) {
        $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
        $this->setError($e);
        return false;
      }

      // Tick the availability progress flag to true
      JApplication::setUserState('com_helloworld.facilities.progress', true);

      return true;
    }
  }

  /**
   * Overloaded check function. This should sanity check the data we are about to insert.
   * Perhaps do this before deleting?
   *
   * @return boolean
   */
  public function check() {
    return true;
  }

}
