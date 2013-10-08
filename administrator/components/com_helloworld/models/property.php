<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorldList Model
 */
class HelloWorldModelProperty extends JModelAdmin {

  public function getForm($data = array(), $loadData = true) {

    $form = $this->loadForm('com_helloworld.helloworld', 'account', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form)) {
      return false;
    }

    return $form;
  }

  public function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.view.listing.data', array());

    return $data;
  }

  public function getTable($name = 'Property', $prefix = 'HelloWorldTable') {
    return JTable::getInstance($name, $prefix);
  }

  /**
   * Returns the expiry date of a property item.
   * @param type $property_id
   * @return type
   */
  public function getPropertyDetails($property_id = '') {

    if (!$property_id) {
      return false;
    }

    // Get the table instance
    $property = $this->getTable('Property', 'HelloWorldTable');

    // And then set the property ID 
    $property->id = $property_id;

    // Load the data up
    if (!$property->load()) {
      return false;
    }

    if (!empty($property->expiry_date)) {
      $date = new JDate($property->expiry_date);
      $expiry_date = $date->toUnix();
    } else {
      $expiry_date = false;
    }


    return $expiry_date;
  }
  
  
}
