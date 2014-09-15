<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class PropertyControllerListing extends JControllerForm
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

    $this->registerTask('checkin', 'review');
  }

  public function accountupdate()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $app = JFactory::getApplication();
    $model = $this->getModel('Payment');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.edit.$this->context";

    // Get the record ID from the data array
    $recordId = $this->input->getInt('id', '', 'int');

    // Check that the edit ID is in the session scope
    if (!$this->checkEditId($context, $recordId))
    {
      return false;
    }

    if (!$this->validate($model, $data, $context, $recordId))
    {

      // Save the data in the session.
      $app->setUserState($context . '.data', $data);

      // Redirect back to the edit screen.
      $this->setRedirect(
              JRoute::_('index.php?option=com_rental&view=payment&layout=account&id=' . (int) $recordId, false)
      );
      return false;
    }

    // Need to do a lookup from the model.
    $record = $this->getModel('Property')->getItem($recordId);

    if (empty($record))
    {
      return false;
    }

    $ownerId = $record->created_by;

    $data['user_id'] = $ownerId;

    $profile = $this->getModel('UserProfile');

    if (!$profile->save($data))
    {
      return false;
    }

    $message = JText::_('COM_RENTAL_ACCOUNT_DETAILS_UPDATED');
    $redirect = JRoute::_('index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId, false);
    $this->setRedirect($redirect, $message, 'success');

    return true;
  }

  /**
   * Method to check if you can View a record/resource.
   * TO DO - Expand to check if listing is checked out to a user...
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowView($id = 0)
  {

    // Initialise variables.
    $user = JFactory::getUser();
    $userId = $user->get('id');
    $ownerId = '';

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
      if (empty($ownerId) && $id)
      {
        // Need to do a lookup from the model.
        $record = $this->getModel('Property')->getItem($id);
        if (empty($record) || $record->review == 2)
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
   * Approve updates controller action 
   */
  public function approve()
  {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    // TO DO - Add permissions check here
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable();

    $input = JFactory::getApplication()->input;
    $recordId = $input->get('id', '', 'int');
    $checkin = property_exists($table, 'checked_out');

    $recordId = $input->get('id', '', 'int');

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=review&layout=approve&property_id=' . $recordId, false
            )
    );
    return true;
  }

  /**
   * 
   */
  public function publish()
  {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    // Authorisation check. Check that this user is allowed to publish
    if (!$this->allowView())
    {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    $app = JFactory::getApplication();
    $input = $app->input;
    $recordId = $input->get('id', '', 'int');
    $data = $input->get('jform', '', array());

    // Get the various models we will be using
    $model = $this->getModel();
    $model->setState('com_rental.listing.id', $recordId);
    $property_model = $this->getModel('Property', 'RentalModel');
    $listingreview_model = $this->getModel('Review', 'RentalModel');

    // Validate the posted data
    $form = $listingreview_model->getForm();

    // Test whether the data is valid.
    $validData = $listingreview_model->validate($form, $data);

    // Check for validation errors.
    if ($validData === false)
    {
      // Get the validation messages.
      $errors = $model->getErrors();

      // Push up to three validation messages out to the user.
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
      {
        if (($errors[$i]) instanceof Exception)
        {
          $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
        }
        else
        {
          $app->enqueueMessage($errors[$i], 'warning');
        }
      }

      // Redirect back to the edit screen.
      $this->setRedirect(
              JRoute::_('index.php?option=' . $this->option . '&view=listingreview&layout=approve&property_id=' . (int) $recordId, false)
      );

      return false;
    }

    // Get Items returns an array of units which represents the listing
    $listing = $model->getItems();

    // Updates the review status for all units and property
    $publish = $model->publishListing($listing);

    if (!$publish)
    {
      // TO DO - Log and determine action
      return false;
    }

    // Get a new instance of the properyt model and checkin the record
    $property_model->checkin(array($recordId));

    // Send the confirmation email
    $mail = $model->sendApprovalEmail($listing, $validData);

    // Send confirmation email
    $msg = JText::sprintf('COM_RENTAL_PROPERTY_PUBLISHED', $listing[0]->id);
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option, false
            ), $msg, 'success'
    );
    return true;
  }

  /**
   * review controller action - handles the case when a user wants to review the changes to a listing.
   * 
   * 
   */
  public function review()
  {
    // Check that this is a valid call from a logged in user.
    if (!JSession::checkToken() && !JSession::checkToken('GET'))
    {
      die('Invalid Token');
    }

    // Get the user
    $user = JFactory::getUser();
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable('Property', 'RentalTable');
    $cid = $this->input->post->get('cid', array(), 'array');
    $app = JFactory::getApplication();
    $context = "$this->option.edit.review";

    $recordId = (int) (count($cid) ? $cid[0] : $app->input->get('property_id', ''));
    $checkin = property_exists($table, 'checked_out');

    // Check user is authed to review
    if (!$user->authorise('rental.listing.review', $this->option))
    {

      // Set the internal error and also the redirect error.
      $this->setError(JText::_('COM_RENTAL_PROPERTY_REVIEW_NOT_AUTHORISED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    // Check property out to user reviewing
    if ($checkin && !$model->checkout($recordId))
    {
      // Check-out failed, display a notice but allow the user to see the record.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false
              )
      );

      return false;
    }
    else
    {
      // Check-out succeeded, push the new record id into the session.
      $this->holdEditId($context, $recordId);
      $app->setUserState($context . '.data', null);

      $this->view_item = 'propertyversions';

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=review&layout=property&property_id=' . $recordId, false
              )
      );

      return true;
    }
  }

  /**
   * 
   * @return boolean
   */
  public function release()
  {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    // Get the user
    $user = JFactory::getUser();
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable('Property', 'RentalTable');
    $input = JFactory::getApplication()->input;

    $recordId = $input->get('id', '', 'int');

    $checkin = property_exists($table, 'checked_out');

    // TO DO - CHECK Edit id is in the session for this user
    // Check-in the original row.
    if ($checkin && $model->checkin($recordId) === false)
    {
      // Check-in failed. Go back to the item and display a notice.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false
              )
      );

      return false;
    }
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option, false
            )
    );
    return true;
  }

  public function stats()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // Get the id of the property being statted
    $id = JFactory::getApplication()->input->getInt('id');

    if (!$this->allowView($id))
    {

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

  /**
   * Submit - controller to determine where to go when a listing in submitted for review
   *
   * @return boolean
   * 
   */
  public function submit()
  {

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel('Submit');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.edit.$this->context";
    $task = $this->getTask();
    jimport('frenchconnections.models.payment');
    $user = JFactory::getUser();

    // Get the record ID from the data array
    $recordId = $this->input->post->get('property_id', '', 'int');

    // Check that the edit ID is in the session scope
    if (!$this->checkEditId($context, $recordId))
    {
      return false;
    }

    // Validate the posted data.
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getForm($data, false);

    if (!$form)
    {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
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

      $app->setUserState($context . '.data', $data);


      // Redirect back to the edit screen.
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=listing'
                      . $this->getRedirectToItemAppend($recordId, 'id'), false
              )
      );

      return false;
    }

    // TO DO - Save the submittions note into the notes table, if there is a note.
    // It's all good.
    // Here we need to determine how to handle this submission for review.
    // If the expiry date is within 7 days then we need to redirect the user to the renewal screen.
    // If the property has expired then we need to redirect the user to the renewal screen (or they will click the renewal button)
    // 
    // If a new property then same as above? No, expiry date should only be set after the review. But they do need to pay...
    // 
    // If not expired then we need to determine if they have added any new billable items
    // 
    // Otherwise, should just be submitted to the PFR and locked for editing.
    // 
    // Redirect to the renewal payment/summary form thingy...

    $listing = $this->getModel('Listing', 'RentalModel', $config = array('ignore_request' => true));
    $listing->setState('com_rental.listing.id', $recordId);
    $listing->setState('com_rental.listing.latest', true);

    // Get the listing unit details
    $current_version = $listing->getItems();

    $days_to_renewal = RentalHelper::getDaysToExpiry($current_version[0]->expiry_date);

    // TO DO - Could the following be moved into a separate method in the model?
    if (empty($current_version[0]->vat_status))
    {
      // No VAT status on record for this listing.
      $message = JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_BLURB');
      $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&layout=account&id=' . (int) $recordId, false);
      $this->setRedirect($redirect, $message);
    }
    elseif ($days_to_renewal < 7 && $days_to_renewal > 0)
    {
      // If there are between 7 and 0 days to renewal  
      $message = ($days_to_renewal > 0) ? 'Your property is expiring within 7 days - please renew now' : 'Property expired, renew now.';
      $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&id=' . (int) $recordId . '&renewal=1', false);
    }
    else if (empty($days_to_renewal))
    {
      // New property 
      $message = JText::_('COM_RENTAL_PAYMENT_DUE_BLURB');
      $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&id=' . (int) $recordId, false);
    }
    else if ((!empty($days_to_renewal) && $days_to_renewal < 0 && $current_version[0]->review)) // Need to check review status here...
    {

      $message = JText::_('COM_RENTAL_PAYMENT_DUE_FOR_RENEWAL_WITH_CHANGES');
      $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&id=' . (int) $recordId . '&renewal=1', false);
    }
    else
    {
      // Need to determine whether they owe us any more wedge
      // This sets a state flag in the model to ensure we get a list of what is currently published.
      $listing->setState('com_rental.listing.latest', false);
      $previous_version = $listing->getItems();
      $payment = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $current_version, 'renewal' => false));
      $order_summary = $payment->getPaymentSummary($current_version, $previous_version);

      if ($order_summary)
      {
        // Redirect to payment screen
        $message = JText::_('COM_RENTAL_PAYMENT_DUE_FOR_PAYMENT_WITH_CHANGES');
        $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&id=' . (int) $recordId, false);
      }
      else
      {
        // If we get here it means there is no payment due for this so we just lock it for editing.
        $payment->updateProperty($listing_id = $current_version[0]->id, 0, 2);
        $message = JText::_('COM_RENTAL_NO_PAYMENT_DUE_WITH_CHANGES');

        if (RentalHelper::isOwner($user->id))
        {
          $redirect = JRoute::_('index.php');
        }
        else
        {
          $redirect = JRoute::_('index.php?option=' . $this->extension);
        }
      }
    }

    $this->setRedirect($redirect, $message);

    return true;
  }

  public function getModel($name = 'Property', $prefix = '', $config = array())
  {
    // Add the component model path
    $this->addModelPath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/models/', ucfirst($this->model_prefix));
    
    // Get an instance of the model 
    return parent::getModel($name, ucfirst($this->model_prefix), $config);
  }

  public function validate($model, $data, $context, $recordId)
  {

    $app = JFactory::getApplication();

    // Validate the posted data.
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getForm($data, false);

    if (!$form)
    {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
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

      $app->setUserState($context . '.data', $data);



      return false;
    }
    return true;
  }

}
