<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerPropertyVersions extends JControllerForm {

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

  /*
   * Overload the cancel method
   */

  public function cancel($key = null) {
    if (parent::cancel($key)) {
      $app = JFactory::getApplication();
      $recordId = $app->input->getInt('parent_id');

      $this->view_list = ($recordId) ? 'listing' : 'properties';

      if ($recordId > 0) {
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_list . '&id='
                        . (int) $recordId, false
                )
        );
      }
    }
  }

  /*
   * Override the save method in order to redirect
   *
   */

  public function save($key = null, $urlVar = null) {
    if (parent::save($key, $urlVar)) {

      $task = $this->getTask();
      if ($task == 'save') {
        $app = JFactory::getApplication();
        $recordId = $app->input->getInt('parent_id');

        $this->view_list = ($recordId) ? 'units' : 'properties';

        if ($recordId > 0) {
          $this->setRedirect(
                  JRoute::_(
                          'index.php?option=' . $this->option . '&view=' . $this->view_list . '&id='
                          . (int) $recordId, false
                  )
          );
        }
      }
    }
  }

}
