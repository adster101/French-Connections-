<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// Include the utility class which extends controllerform
//include_once('utility.php');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerListing extends JControllerForm {

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
        $record = $this->getModel('Property')->getItem($recordId);
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

  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   *
   */

  public function view() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // $id is the listing the user is trying to edit
    $id = $this->input->get('id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id')) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }


    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false)
    );
    return true;
  }

  public function ordersummary() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $app = JFactory::getApplication();
    $records = $this->input->get('cid', array(), 'array');
    $recordId = $records[0];
    $model = $this->getModel('Property', 'HelloWorldModel');
    $table = $model->getTable();
    $context = $this->extension . '.' . $this->view_list . '.' . 'summary';

    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    // Check that the owner/user can edit/renew this record
    if (!$this->allowEdit(array($key => $recordId), $key)) {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->extension, false
              )
      );

      return false;
    }


    // User is allowed to edit this resource, push the new record id into the session.
    $this->holdEditId($context, $recordId);

    // Redirect to the renewal payment/summary form thingy...
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->extension . '&view=renewal&id=' . (int) $recordId, false
            )
    );



    return false;
  }

  public function collectpayment() {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel();
    $table = $model->getTable();
    $data = $this->input->post->get('jform', array(), 'array');

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

    // The save2copy task needs to be handled slightly differently.
    if ($task == 'save2copy') {
      // Check-in the original row.
      if ($checkin && $model->checkin($data[$key]) === false) {
        // Check-in failed. Go back to the item and display a notice.
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

      // Reset the ID and then treat the request as for Apply.
      $data[$key] = 0;
      $task = 'apply';
    }

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
