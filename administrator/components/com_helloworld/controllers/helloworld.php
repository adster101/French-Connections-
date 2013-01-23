<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerHelloWorld extends JControllerForm {

  protected $extension;

  /**
   * Constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  1.6
   * @see    JController
   */
  public function __construct($config = array()) {
    parent::__construct($config);

    // Guess the JText message prefix. Defaults to the option.
    if (empty($this->extension)) {
      $this->extension = JRequest::getCmd('extension', 'com_helloworld');
    }
  }

  /**
   * Method to check if you can edit a record.
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {
    // Initialise variables.
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // This covers the case where the user is creating a new property (i.e. id is 0 or not set
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension)) {
      return true;
    }

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->extension)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension)) {
      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        $record = $this->getModel()->getItem($recordId);

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

  /**
   * Method to add a new record. This is picked up before redirecting to the new property view
   *
   * @return  mixed  True if the record can be added, a error object if not.
   *
   * @since   12.2
   */
  public function addnew() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $user = JFactory::getUser();
    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";

    // Access check.
    if (!$this->allowAdd()) {
      // Set the internal error and also the redirect error.
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    // Check the users permissions to assign ownership, no perm, not admin
    if ($user->authorise('helloworld.edit.property.owner', 'com_helloworld')) {

      // Check if the form and a user has been submitted
      $data = JRequest::getVar('jform', '', 'POST');

      // is $data set?
      if (!empty($data)) {

        if (array_key_exists('created_by', $data) && $data['created_by'] != 0) {

          // Store the created_by chosen in the session, we check this in the preprocess form method of the helloworld model
          JApplication::setUserState('created_by', $data['created_by']);

          JApplication::setUserState('parent_id', $data['parent_id']);

          $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=helloworld.edit', false));

          return false;
        }

        $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_CHOOSE_AN_OWNER'), 'error');
      }

      // Redirect to the choose owner screen.
      $this->setRedirect(
              JRoute::_('index.php?option=' . $this->option . '&view=new&layout=chooseowner', false)
      );
    } else {

      // Check if the form and a user has been submitted
      $data = JRequest::getVar('jform', '', 'POST');

      // is $data set?
      if (!empty($data)) {

        if (array_key_exists('parent_id', $data) && $data['parent_id'] != 0) {

          // Store the created_by chosen in the session, we check this in the preprocess form method of the helloworld model
          JApplication::setUserState('parent_id', $data['parent_id']);

          $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=helloworld.edit', false));
          if ($data['parent_id'] == 1) {
            $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_TO_BE_ADDED'), 'notice');
          } else {
            $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_TO_BE_ADDED'), 'notice');
          }

          return false;
        }
      }

      // Redirect to the choose owner screen.
      $this->setRedirect(
              JRoute::_('index.php?option=' . $this->option . '&view=new&layout=default', false)
      );
    }


    // Clear the record edit information from the session.
    $app->setUserState($context . '.data', null);



    return true;
  }
    

}
