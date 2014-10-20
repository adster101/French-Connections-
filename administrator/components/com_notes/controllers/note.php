<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class NotesControllerNote extends JControllerForm
{

  /**
   * Method to add a new record.
   *
   * @return  mixed  True if the record can be added, a error object if not.
   *
   * @since   12.2
   */
  public function add()
  {
    parent::add();

    $app = JFactory::getApplication();

    $property_id = $app->input->get('property_id', '', 'int');

    // Redirect to the edit screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($property_id, 'property_id'), false
            )
    );

    return true;
  }

  public function postSaveHook(\JModelLegacy $model, $validData = array())
  {
    // Get the contents of the request data
    $input = JFactory::getApplication()->input;
    // If the task is save and next
    // Check if we have a next field in the request data
    $return = $input->get('return', '', 'base64');
    // And set the redirect if we have
    if ($return)
    {
      $this->setRedirect(base64_decode($return));
    }
  }

}
