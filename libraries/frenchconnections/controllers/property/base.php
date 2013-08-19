<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// TO DO - extend this controller and other that extend from controller form
// from another generic class which contain canEdit instead of defining in each controller
// Or simply import a utility class with this and other useful methods in
// from the libraries folder

/**
 * HelloWorld Controller
 */
class HelloWorldControllerBase extends JControllerForm {

  protected function allowEdit($data = array(), $key = 'property_id') {
    $user = JFactory::getUser();
    $userId = $user->get('id');
    
    $this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_helloworld/models', 'HelloWorldModel');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables', 'HelloWorldTable');

    // Needs to be update as follows
    // For enquiries and special offers the $key will not point to the property table
    if ($this->context == 'enquiry' || $this->context == 'unitversions') {

      $model = $this->getModel();
      $table = $model->getTable();

      if (!property_exists($table, $key)) {
        return false;
        
        
      } else {
        $table->load($data[$key]);
        $recordId = $table->property_id;
      }
    } else {

      // Initialise variables.
      $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    }
    // If we don't have a property ID then we can't authorise
    if ($recordId === 0) {
      return false;
    }

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->option)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->option)) {
      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        
        $record = $this->getModel('Property','HelloWorldModel')->getItem($recordId);
        if (empty($record)) {
          return false;
        }
        $ownerId = $record->created_by;
      }

      // If the owner matches 'me' then do the test.
      if ($ownerId == $userId) {

        return true;
      }
    }
    return false;
  }

}