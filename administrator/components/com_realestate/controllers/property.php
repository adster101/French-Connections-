<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class RealEstateControllerProperty extends JControllerForm
{

  protected function postSaveHook(\JModelLegacy $model, $validData = array())
  {
    // Just redirect back to the listings view.
    $this->setRedirect('index.php?option=com_realestate');
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
    $model = $this->getModel('Property');
    $data = $this->input->post->get('jform', array(), 'array');
 		$cid   = $this->input->post->get('cid', array(), 'array');
    $property_id = $this->input->post->get('property_id', '', 'int');

    $context = "$this->option.edit.$this->context";
    
    $user = JFactory::getUser();

    // Get the record ID from the data array
		$recordId = (int) (count($cid) ? $cid[0] : $property_id);

    // Check whether this user owns this record
    if (!PropertyHelper::allowEditRealestate($id))
    {
      return false;
    }

    // Validate the posted data.
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getSubmitForm($data, false);

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

    $days_to_renewal = PropertyHelper::getDaysToExpiry($current_version[0]->expiry_date);

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

}
