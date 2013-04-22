<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerProperty extends JControllerForm {

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

  public function postSaveHook(JModelLegacy $model, $validData = array()) {

    // Various things we could do here...
    // For now we want to push the property listing progress into the session
  }

  /*
   * renew action prepares a property for renewal...
   * 
   * 
   */

  public function renew($data = array()) {

    $app = JFactory::getApplication();

    $records = $this->input->get('cid', array(), 'array');
    $recordId = $records[0];
    $model = $this->getModel();
    $table = $model->getTable();

    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    

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
    
    $model->getFullListingDetails($recordId);
    
    
    
    
    // 1. Load the property listing details (including units) using a full join on published units.
    // 2. Ensure that the expiry date has indeed expired and also that there is an expiry date (no expiry date means not a renewal).
    // 3. Check that there are no pending changes which need to be reviewed. If there are then need to get the owner to submit property for review before proceeding to payment
    // 4. Work out how much needs to be paid for the renewal.
    // 5. Show the payment form
    // 6. Process the payment details
    // 7. Update the expiry date, generate invoice from based on the order
    // 8. Show confirmation page, generate renewal thankyou email?    

    
    return false;
    
    
  }

}
