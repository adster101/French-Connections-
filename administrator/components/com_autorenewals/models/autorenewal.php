<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class AutorenewalsModelAutoRenewal extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Property', $prefix = 'RentalTable', $config = array()) {
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Abstract method for getting the form from the model.
   *
   * @param   array    $data      Data for the form.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  mixed  A JForm object on success, false on failure
   *
   * @since   12.2
   */
  public function getForm($data = array(), $loadData = true) {
    
  }

  public function publish($id = '') {

    if (empty($id)) {
      return false;
    }

    // Loop over each ID and update the property listing accordingly.
    // Should really do an access check here, but component is locked down to admin only

    foreach ($id as $k => $v) {

      $data['id'] = $v;
      $data['VendorTxCode'] = '';
      
      // Call the save method which use getTable to determine the table to update. 
      if (!$this->save($data)) {
        return false;
      }
    }

    return true;
  }

}