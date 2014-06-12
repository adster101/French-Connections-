<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Controller
 */
class RentalControllerRenewal extends JControllerLegacy {

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
      $this->extension = JRequest::getCmd('extension', 'com_rental');
    }
  }

  public function summary() {

    // Get the record ID being renewed
    $recordId = $this->input->get('id', '', 'int');

    // Set the context so we can hold the edit ID
    $context = "com_rental.renewal.summary";

    // Check that the owner/user can edit/renew this record
    if (!$this->allowEdit(array('id' => $recordId))) {
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
                    'index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId . '&renewal=1', false
            )
    );

    return false;
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
   *  Method to process the card details for a renewal payment...actual payment processing is done in the model...
   *
   */

  public function process() {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $app = JFactory::getApplication();
    
    $id = $this->input->get('id', '', 'int');
    
    // Get an instance of the listing model
    $listing_model = JModelLegacy::getInstance('Listing', 'RentalModel', $config = array('ignore_request' => true));

    // Set the listing ID we are processing payment for
    $listing_model->setState('com_rental.listing.id', $id);

    // Get the listing details (i.e. a list of units that make up the listing
    $listing = $listing_model->getItems();

    // Instantiate an instance of the property model using the listing detail as the config
    $model = $this->getModel('Payment', 'RentalModel', $config = array('listing' => $listing));
    $form = $model->getPaymentForm();

    // Data here is the clients billing address details
    $data = $this->input->post->get('jform', array(), 'array');

    // TO DO: Add another check here to make sure the user is authed to do this
    // Validate the posted data.
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
      $app->setUserState('com_rental.renewal.data', $data);

      // Redirect back to the edit screen.
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=renewal&layout=payment&id=' . (int) $data['id'], false));
      return false;
    }
    // import our payment library class
    jimport('frenchconnections.models.payment');

    $model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $listing, 'renewal' => true));

    // Attempt to save the configuration.
    $return = $model->processPayment($validData);

    // Check the return value.
    if ($return === false) {
      // Save the data in the session.
      $app->setUserState('com_rental.renewal.data', $data);

      // Save failed, go back to the screen and display a notice.
      $message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
      $this->setRedirect('index.php?option=com_rental&view=payment&layout=payment&id=' . (int) $data['id'], $message, 'error');
      return false;
    }

    // Payment has been authorised...
    $message = $model->processListing($return, $validData);

    // Empty the data stored in the session...
    $app->setUserState('com_rental.renewal.data', $data);

    // $return should contain a redirect url and a message, at least
    // Set the redirect based on the task.
    switch ($this->getTask()) {

      default:
        $this->setRedirect('index.php?option=com_rental', $message);
        break;
    }

    return true;
  }

}
