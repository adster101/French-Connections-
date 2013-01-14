<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class EnquiriesControllerEnquiry extends JControllerForm {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {

    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit')) {
      return true;
    }

    // Check edit own here so that they cannot edit anothers enquiry
    

    return false;
  }

}
