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
class RentalControllerBase extends JControllerForm
{

  protected function allowEdit($data = array(), $key = 'property_id')
  {

    $user = JFactory::getUser();
    $userId = $user->get('id');

    $this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_rental/models', 'RentalModel');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables', 'RentalTable');

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->option))
    {
      return true;
    }

    // For enquiries and special offers, look up the property id using the table
    if ($this->context == 'availability' || $this->context == 'images' || $this->context == 'specialoffer' || $this->context == 'unitversions' || $this->context == 'tariffs')
    {
      // Get the unit versions model and instance of the table. 
      // This is because these views are based around the unit_id. 
      // TO DO - This controller file can be removed and this method added to RentalHelper...
      $model = $this->getModel('UnitVersions', 'RentalModel');
      $table = $model->getTable();

      if (!property_exists($table, 'property_id'))
      {
        return false;
      }

      $table->load($data[$key]);
      $recordId = $table->property_id;
    }  
    else
    {
      // Initialise variables.
      $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    }

    // If we don't have a property ID then we can't authorise
    if ($recordId === 0)
    {
      return false;
    }

    // TO DO - This is clunky. We probably don't need this controller at least for enquiry and invocie
    // views. Would probably be enough to add the below into the 'property' helper class and call it 
    // from each controller.
    
    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->option))
    {
      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId)
      {
        // Need to do a lookup from the model.
        $record = $this->getModel('Property', 'RentalModel')->getItem($recordId);
        if (empty($record))
        {
          return false;
        }
        $ownerId = $record->created_by;
      }

      // If the owner matches 'the owner' then do the test.
      if ($ownerId == $userId)
      {

        return true;
      }
    }
    return false;
  }

}