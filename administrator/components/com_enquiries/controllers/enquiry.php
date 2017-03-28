<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Enquiry Controller
 */
class EnquiriesControllerEnquiry extends JControllerForm
{

  /**
   * allowEdit -
   *
   * @param type $data
   * @param type $key
   * @return boolean
   */
  protected function allowEdit($data = array(), $key = 'property_id')
  {

    $user = JFactory::getUser();
    $userId = $user->get('id');

    $this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_rental/models', 'RentalModel');
    $this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_realestate/models', 'RealEstateModel');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables', 'RentalTable');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/tables', 'RealEstateTable');

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->option))
    {
      return true;
    }

    // Look up the enquiry details so we can get the property ID
    $model = $this->getModel();
    $id = $this->input->getInt('id');
    $item = $model->getItem($id);

    $recordId = (int) !empty($item->property_id) ? $item->property_id : 0;

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

        if (empty($record->id))
        {
          // No record found against the property list so perhaps it a realestate enquiry...
          $record = $this->getModel('Property', 'RealestateModel')->getItem($recordId);

          // If still no record then bail!
          if (empty($record))
          {
            return false;
          }
        }

        // Carry on!
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

  /**
   * This method extends the edit method and updates the state to 'read'
   *
   * @param type $key
   * @param type $urlVar
   * @return boolean
   */
  public function edit($key = null, $urlVar = null)
  {

    if (parent::edit($key, $urlVar))
    {

      // Update the status of the enquiry to indicate that it's been read.
      $model = $this->getModel();
      $id = $this->input->getInt('id');
      $item = $model->getItem($id);

      if (!$item)
      {
        return false;
      }

      // Only publish the item (mark as read) if the state is unread and not failed
      if ($item->state == 0)
      {
        $model->publish($id);
        return true;
      }
    }
    return true;
  }

  /*
   * Function to reply to an owner enquiry.
   * Updates a date field in the enquiries table to indicate the owner replied.
   *
   *
   */

  public function reply()
  {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Get the necessary details to process the action
    $app = JFactory::getApplication();
    $data = $this->input->post->get('jform', array(), 'array');
    $model = $this->getModel();
    $context = "$this->option.edit.$this->context";
    $recordId = $data['id'];
    $urlVar = 'id';

    /*
     * Check that the user holds this id in their session, otherwise we bounce it back to the list view
     */
    if (!$this->checkEditId($context, $recordId))
    {
      // Somehow the person just went to the form and tried to save it. We don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );
      return false;
    }

    // Validate the posted data.
    $form = $model->getForm($data, false);

    if (!$form)
    {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
    $validData = $model->validate($form, $data);

    // Check for validation errors.
    if ($validData === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();

      // Push up to five validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 5; $i++)
      {
        if ($errors[$i] instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'warning');
        }
      }

      // Save the data in the session.
      $app->setUserState($context . '.data', $data);

      // Redirect back to the edit screen.
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_item
                      . $this->getRedirectToItemAppend($recordId, $urlVar), false
              )
      );

      return false;
    }



    // TO DO - Get the property and user contact details here.
    // Need to determine whether they have overridden the contact details or whether to use the default invoice contact details...
    // Also, need to verify the user sending the reply is the owner. So below needs to go into the enquiry model

    if (!$model->sendReply($validData))
    {
      // Redirect back to the edit screen.

      $this->setMessage(JText::_('COM_ENQUIRIES_PROBLEM_SENDING_EMAIL'), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_item
                      . $this->getRedirectToItemAppend($recordId, $urlVar), false
              )
      );

      return false;
    }

    // Clear the record id and data from the session.
    $this->releaseEditId($context, $recordId);
    $app->setUserState($context . '.data', null);

    $this->setMessage(JText::_('COM_ENQUIRIES_ENQUIRY_REPLY_SENT'));

    // Redirect to the list screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
            )
    );

    return true;
  }

}
