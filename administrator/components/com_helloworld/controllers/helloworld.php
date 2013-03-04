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

  /**
   * Method to save a record.
   *
   * @param   string  $key     The name of the primary key of the URL variable.
   * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
   *
   * @return  boolean  True if successful, false otherwise.
   *
   * @since   12.2
   */
  public function save($key = null, $urlVar = null) {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel();
    $table = $model->getTable();
    $data = $this->input->post->get('jform', array(), 'array');
    $checkin = property_exists($table, 'checked_out');
    $context = "$this->option.edit.$this->context";
    $task = $this->getTask();

    // A list of fields that should trigger a new version if they are different to existing record
    $fields_to_check = array('title', 'description', 'location_details', 'getting_there');
    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    // To avoid data collisions the urlVar may be different from the primary key.
    if (empty($urlVar)) {
      $urlVar = $key;
    }

    $recordId = $this->input->getInt($urlVar);

    if (!$this->checkEditId($context, $recordId)) {
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

    // Populate the row id from the session.
    $data[$key] = $recordId;

    // Access check.
    if (!$this->allowSave($data, $key)) {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
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
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getForm($data, false);

    if (!$form) {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
    $validData = $model->validate($form, $data);

    // Check for validation errors.
    if ($validData === false) {
      // Get the validation messages.
      $errors = $model->getErrors();

      // Push up to three validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
        if ($errors[$i] instanceof Exception) {
          $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
        } else {
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

    // Here we are at point where we can determine if we need to generate a new version or not.
    // Firstly check against the expiry date. We use the POST data and not the filtered form data as the expirty date is unset
    if (!empty($data['expiry_date'])) {
      
      // Property has an expiry date...
      if (!empty($data['published'])) {
        // Case through the various published states
        
        SWITCH ($data['published']) {
          CASE 1:
            // This is a property that needs to be dealt with in a new version 
            $version = true;
            
            break;
          CASE 0:
            // This property can be published directly
            $version = false; 
          DEFAULT:
            $version = false;
            break;
        }
      }
    }




    // Attempt to save the data.
    if (!$model->save($validData)) {
      // Save the data in the session.
      $app->setUserState($context . '.data', $validData);

      // Redirect back to the edit screen.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_item
                      . $this->getRedirectToItemAppend($recordId, $urlVar), false
              )
      );

      return false;
    }

    // Save succeeded, so check-in the record.
    if ($checkin && $model->checkin($validData[$key]) === false) {
      // Save the data in the session.
      $app->setUserState($context . '.data', $validData);

      // Check-in failed, so go back to the record and display a notice.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_item
                      . $this->getRedirectToItemAppend($recordId, $urlVar), false
              )
      );

      return false;
    }

    $this->setMessage(
            JText::_(
                    ($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
            )
    );

    // Redirect the user and adjust session state based on the chosen task.
    switch ($task) {
      case 'apply':
        // Set the record data in the session.
        $recordId = $model->getState($this->context . '.id');
        $this->holdEditId($context, $recordId);
        $app->setUserState($context . '.data', null);
        $model->checkout($recordId);

        // Redirect back to the edit screen.
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
        );
        break;

      case 'save2new':
        // Clear the record id and data from the session.
        $this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);

        // Redirect back to the edit screen.
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend(null, $urlVar), false
                )
        );
        break;

      default:
        // Clear the record id and data from the session.
        $this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);

        // Redirect to the list screen.
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_list
                        . $this->getRedirectToListAppend(), false
                )
        );
        break;
    }

    // Invoke the postSave method to allow for the child class to access the model.
    $this->postSaveHook($model, $validData);

    return true;
  }

}
