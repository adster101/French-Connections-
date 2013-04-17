<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerSnooze extends JControllerForm {

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

    $this->view_list = 'properties';
  }

  /**
   * Overridden edit method to edit an existing record.
   *
   * @return  boolean  True if access level check and checkout passes, false otherwise.
   *
   * @since   12.2
   */
  public function update() {

    $user   = JFactory::getUser();
    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";
    $records = $this->input->get('cid', array(), 'array');
    $recordId = $records[0];

    // Access check.
    if (!$user->authorise('helloworld.snooze', $this->option)) {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=property'
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    
    // User authorised, Hurray!
    $this->holdEditId($context, $recordId);
    $app->setUserState($context . '.data', null);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, 'id'), false
            )
    );

    return true;
  }

  /**
   * Method to check if you can update the snooze status for a property...
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {
    return JFactory::getUser()->authorise('helloworld.snooze', $this->option);
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
    
    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    // To avoid data collisions the urlVar may be different from the primary key.
    if (empty($urlVar)) {
      $urlVar = $key;
    }

    $recordId = $data['id'];


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

    // Attempt to save the data. This will only save the 'property' related model data
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

    // Need to additionally get the contact log model and save the contact log details.
    $contact_model = JTable::getInstance('ContactLog', 'HelloWorldTable');

    $contact_model->property_id = $data['id'];
    $contact_model->body = $data['body'];
    $contact_model->subject = $data['subject'];
    $contact_model->created_time = JFactory::getDate()->toSql();
    $contact_model->created_user_id = JFactory::getUser()->id;
    
    if (!$contact_model->store()) {
      // Check-in failed, so go back to the record and display a notice.
      $this->setError(JText::sprintf('COM_PROPERTY_SAVE_CONTACT_LOG_ERROR', $model->getError()));
      $this->setMessage($this->getError(), 'error');
      // Redirect to the list screen.
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );
    }


    $this->setMessage(
            JText::_(
                    ($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
            )
    );


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


    // Invoke the postSave method to allow for the child class to access the model.
    $this->postSaveHook($model, $validData);

    return true;
  }

}
