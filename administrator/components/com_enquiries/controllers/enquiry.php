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
		
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    
 		$user = JFactory::getUser();
		$userId = $user->get('id');
    
    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit','com_enquiries')) {
      return true;
    }
    
    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit.own','com_enquiries')) {

      // They have permission to edit own, but do they own?
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
            
      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        $record = $this->getModel()->getItem($recordId);
        if (empty($record))
        {
          return false;
        }

        $ownerId = $record->owner_id;
      }

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
      
      return false;
    }    
    return false;
  }

}
