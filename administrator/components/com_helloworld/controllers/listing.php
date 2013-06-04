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
   * Method to check if you can View a record/resource.
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowView($id = 0) {

    // Initialise variables.
    $user = JFactory::getUser();
    $userId = $user->get('id');
    $ownerId = '';

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->extension)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension)) {
      // Now test the owner is the user.
      if (empty($ownerId) && $id) {
        // Need to do a lookup from the model.
        $record = $this->getModel('Property')->getItem($id);
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

    $context = "$this->option.view.$this->context";

    // $id is the listing the user is trying to edit
    $id = $this->input->get('id', '', 'int');

    if (!$this->allowView($id)) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }

    // Hold the edit ID once the id and user have been authorised.
    $this->holdEditId($context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false)
    );
    return true;
  }

  public function stats() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // Get the id of the property being statted
    $id = JFactory::getApplication()->input->getInt('id');

    if (!$this->allowView($id)) {

      echo JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id);
      jexit();
    }

    $this->holdEditId($this->option . '.stats.view', $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=stats&tmpl=component&id=' . (int) $id, false)
    );


    return true;
  }

  public function submit() {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel('Submit');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.view.$this->context";
    $task = $this->getTask();

    // Get the record ID from the data array
    $recordId = $data['id'];

    // Check that the edit ID is in the session scope
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
                      . $this->getRedirectToItemAppend($recordId, 'id'), false
              )
      );

      return false;
    }

    // It's all good.
    // Redirect back to the edit screen.

    // Redirect to the renewal payment/summary form thingy...
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->extension . '&view=renewal&id=' . (int) $recordId, false
            )
    );




    // Hand off to the model to check whether this is a new property
    // Get the details, work out price etc
    // Process payment
    // Update property status (expiry date, review status etc)
    // send confirmation email
    // etc
  }

  /**
   * Gets the URL arguments to append to an item redirect.
   *
   * @param   integer  $recordId  The primary key id for the item.
   * @param   string   $urlVar    The name of the URL variable for the id.
   *
   * @return  string  The arguments to append to the redirect URL.
   *
   * @since   12.2
   */
  protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
    $tmpl = $this->input->get('tmpl');
    $layout = $this->input->get('layout');
    $append = '';

    // Setup redirect info.
    if ($tmpl) {
      $append .= '&tmpl=' . $tmpl;
    }

    if ($layout) {
      $append .= '&layout=' . $layout;
    }

    if ($recordId) {
      $append .= '&' . $urlVar . '=' . $recordId;
    }

    return $append;
  }

}
