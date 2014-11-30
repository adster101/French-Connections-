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

    // It's all good mother fucker!
    // TO DO - Maybe want to redirect to payment view if a new property
    // 
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

}
