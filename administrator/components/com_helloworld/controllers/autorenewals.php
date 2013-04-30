<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerAutoRenewals extends JControllerForm {

  protected $extension;

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

  /*
   * Autorenewal controller action - checks ownership of record and redirects to listing view
   * 
   */

  public function showtransactionlist() {

    $app = JFactory::getApplication();
    $model = $this->getModel();
    $table = $model->getTable();

    $context = "$this->option.edit.$this->context";
    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    $cid = $this->input->post->get('cid', array(), 'array');

    $recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));

    if (!$this->allowEdit(array($key => $recordId), $key)) {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      return false;
    }

    // Set holdEditID etc    
    $this->holdEditId($context, $recordId);
    $app->setUserState($context . '.data', null);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=autorenewals&id=' . (int) $recordId, false)
    );
  }

  /**
   * Method to cancel an edit.
   *
   * @param   string  $key  The name of the primary key of the URL variable.
   *
   * @return  boolean  True if access level checks pass, false otherwise.
   *
   * @since   12.2
   */
  public function cancel($key = null) {
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    $model = $this->getModel();
    $table = $model->getTable();
    $checkin = property_exists($table, 'checked_out');
    $context = "$this->option.edit.$this->context";

    if (empty($key)) {
      $key = $table->getKeyName();
    }

    $recordId = $app->input->getInt($key);

    // Attempt to check-in the current record.
    if ($recordId) {
      // Check we are holding the id in the edit list.
      if (!$this->checkEditId($context, $recordId)) {

        // Somehow the person just went to the form - we don't allow that.
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
    }


    // Clean the session data and redirect.
    $this->releaseEditId($context, $recordId);
    $app->setUserState($context . '.data', null);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option)
    );

    return true;
  }

  public function postSaveHook(JModelLegacy $model, $validData = array()) {

    parent::postSaveHook($model, $validData);

    $this->setMessage(
            JText::sprintf('COM_HELLOWORLD_HELLOWORLD_UPDATED_AUTORENEWAL_DETAILS', $validData['id'])
    );

    // Redirect to the list screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option, false
            )
    );
  }

}
