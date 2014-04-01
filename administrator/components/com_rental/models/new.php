<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelNew extends JModelAdmin {
   /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true) {

    // Get the form.
    $form = $this->loadForm('com_rental.helloworld', 'helloworld', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }
  
  /*
   * Method to get a form for the user to choose which property they would like to add a unit to
   * 
   */

  public function getNewPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_rental.userproperties', 'userproperties', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
  }
  
  /*
   * Method to get a form for the admin user to choose which account they would like to add a property to
   * 
   */

  public function getNewAdminPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_rental.addpropertybyuser', 'addpropertybyuser', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
        
    return $form;
  }
}