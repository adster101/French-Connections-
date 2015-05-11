<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.listing');

/**
 * HelloWorld Controller
 */
class RealEstateControllerListing extends PropertyControllerListing
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
      $this->extension = JRequest::getCmd('extension', 'com_realestate');
    }

    $this->registerTask('checkin', 'review');
    $this->registerTask('publishwithoutemail', 'publish');
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
    $model = $this->getModel('Property');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.edit.$this->context";
    $user = JFactory::getUser();

    // Get the record ID from the data array
    $recordId = $this->input->getInt('id');

    // Check whether this user owns this record or has permissions to submit it for review...
    if (!PropertyHelper::allowEditRealestate($recordId))
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

    $listing = $this->getModel('Listing', 'RealEstateModel', $config = array('ignore_request' => true));
    $listing->setState('com_realestate.listing.id', $recordId);
    $listing->setState('com_realestate.listing.latest', true);

    // Get the listing unit details
    $current_version = $listing->getItems();

    $days_to_renewal = PropertyHelper::getDaysToExpiry($current_version[0]->expiry_date);

    if (empty($days_to_renewal))
    {
      // New property, better request some wedge 
      $message = JText::_('COM_RENTAL_PAYMENT_DUE_BLURB');
      $redirect = JRoute::_('index.php?option=' . $this->extension . '&task=payment.summary&realestate_property_id=' . (int) $recordId, false);
      $this->setRedirect($redirect, $message);
      return true;
    }
    else
    {
      // No payment due here, so just submit for to PFR queue
      // Add a subject to the data array so a note is added to the notes table  
      $message = JText::_('COM_RENTAL_NO_PAYMENT_DUE_WITH_CHANGES');

      // Update the data array 
      $validData['subject'] = JText::_('COM_PROPERTY_SUBMITTED_FOR_REVIEW');
      $validData['review'] = 2;

      if (!$model->save($validData))
      {
        $message = JText::_('COM_REALESTATE_PROPERTY_PROBLEM_SUBMITTING_FOR_REVIEW');
        Throw new Exception($message, 500);
      }

      if (PropertyHelper::isOwner($user->id))
      {
        $redirect = JRoute::_('index.php');
      }
      else
      {
        $redirect = JRoute::_('index.php?option=' . $this->extension);
      }

      $this->setRedirect($redirect, $message);
      return true;
    }

    return true;
  }

  public function snooze24()
  {

    $user = JFactory::getUser();

    $date = JHtml::_('date', '+1 day', 'Y-m-d');

    if (!$user->authorise('realestate.listing.snooze24', 'com_realestate'))
    {
      $this->setMessage(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option)
      );
    }

    $cid = $this->input->post->get('cid', array(), 'array');

    // Get the record ID from the data array
    $recordId = (int) (count($cid) ? $cid[0] : 0);

    $model = $this->getModel('Property');

    $model->save(array('snooze_until' => $date, 'id' => $recordId, 'subject' => JText::sprintf('COM_RENTAL_SNOOZED_24H_NOTE_SUBJECT', $user->name)));

    $this->setMessage(JText::_('COM_RENTAL_PROPERTY_SNOOZED_FOR_24H'));

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option)
    );

    return true;
  }

}
