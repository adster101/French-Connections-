<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Controller
 */
class RentalControllerPayment extends JControllerLegacy
{

  protected $extension;

  /**
   * Constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  1.6
   * @see    JController
   */
  public function __construct($config = array())
  {
    parent::__construct($config);

    // Guess the JText message prefix. Defaults to the option.
    if (empty($this->extension))
    {
      $this->extension = JRequest::getCmd('extension', 'com_rental');
    }
  }

  public function summary()
  {

    // Get the record ID being renewed
    $recordId = $this->input->get('id', '', 'int');

    $listing = $this->getModel('Listing', 'RentalModel', $config = array('ignore_request' => true));
    $listing->setState('com_rental.listing.id', $recordId);
    $listing->setState('com_rental.listing.latest', true);

    // Get the listing unit details
    $current_listing = $listing->getItems();

    // Set the context so we can hold the edit ID
    $context = "com_rental.edit.payment";

    // Get the renewal state
    $renewal = $this->input->get('renewal', 0, 'int');
    $isRenewal = ($renewal) ? '&renewal=1' : '';

    // Check that the owner/user can edit/renew this record
    if (!$this->allowEdit(array('id' => $recordId)))
    {
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
    if (empty($current_listing[0]->vat_status))
    {
      $route = JRoute::_('index.php?option=' . $this->extension . '&view=payment&layout=account&id=' . (int) $recordId . $isRenewal, false, 1);
    }
    else
    {
      // Redirect to the renewal payment/summary form thingy...
      $route = JRoute::_('index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId . $isRenewal, false, 1);
    }

    $this->setRedirect($route);

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
  protected function allowEdit($data = array(), $key = 'id')
  {

    // Initialise variables.
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // This covers the case where the user is creating a new property (i.e. id is 0 or not set
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension))
    {
      return true;
    }

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->extension))
    {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension))
    {
      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId)
      {
        // Need to do a lookup from the model.
        $record = $this->getModel('Property')->getItem($recordId);
        if (empty($record))
        {
          return false;
        }
        $ownerId = $record->created_by;
      }

      // If the owner matches 'me' then do the test.
      if ($ownerId == $userId)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Method to process the card details for a renewal payment...actual payment processing is done in the model...
   * 
   * @return boolean
   */
  public function process()
  {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // import our payment library class
    jimport('frenchconnections.models.payment');
    $previous_version = array();
    $app = JFactory::getApplication();
    $id = $this->input->get('id', '', 'int');
    $renewal = $this->input->get('renewal', false, 'boolean');
    // Get the renewal state
    $isRenewal = ($renewal) ? '&renewal=1' : '';
    // Get an instance of the listing model
    $listing = JModelLegacy::getInstance('Listing', 'RentalModel', $config = array('ignore_request' => true));
    $listing->setState('com_rental.listing.latest', true);
    $user = JFactory::getUser();

    // Set the listing ID we are processing payment for
    $listing->setState('com_rental.listing.id', $id);

    // Get the listing details (i.e. a list of units that make up the listing
    $current_version = $listing->getItems();

    // Instantiate the payment model
    $payment_model = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $current_version, 'renewal' => $renewal));

    // Instantiate an instance of the property model using the listing detail as the config
    $model = $this->getModel('Payment', 'RentalModel');
    $form = $model->getPaymentForm();

    // Data here is the clients billing address details
    $data = $this->input->post->get('jform', array(), 'array');

    // TO DO: Add another check here to make sure the user is authed to do this
    // Validate the posted data.
    $validData = $model->validate($form, $data);

    // Check for validation errors.
    if ($validData === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();

      // Push up to three validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
      {
        if ($errors[$i] instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'warning');
        }
      }

      // Save the data in the session.
      $app->setUserState('com_rental.renewal.data', $data);

      // Redirect back to the edit screen.
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=payment&layout=payment&id=' . (int) $data['id'] . $isRenewal, false));
      return false;
    }

    if (!$renewal)
    {
      $listing->setState('com_rental.listing.latest', false);
      $previous_version = $listing->getItems();
    }

    // Inject the billing details if required
    if ($data['use_invoice_address'])
    {
      // Assume we don't have any card billing details
      $validData = $model->getBillingDetails($validData);
    }

    // Attempt process the payment
    $return = $payment_model->processPayment($validData, $current_version, $previous_version, $this->extension);

    // Check the return value.
    if ($return === false)
    {
      // Save the data in the session.
      $app->setUserState('com_rental.renewal.data', $data);

      // Save failed, go back to the screen and display a notice.
      $message = JText::_($payment_model->getError());
      $this->setRedirect('index.php?option=com_rental&view=payment&layout=payment&id=' . (int) $data['id'] . $isRenewal, $message, 'error');
      return false;
    }

    // Payment has been authorised...TO DO process listing better off somewhere else?
    $message = $payment_model->processListing($return, $validData);

    // Empty the data stored in the session...
    $app->setUserState('com_rental.renewal.data', null);

    if (RentalHelper::isOwner($user->id))
    {
      $this->setRedirect('index.php', $message);
    }
    else
    {
      $this->setRedirect('index.php?option=com_rental', $message);
    }

    return true;
  }

}

