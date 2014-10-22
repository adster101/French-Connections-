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
    JModelLegacy::addIncludePath(JPATH_LIBRARIES . '/frenchconnections/models');
    JTable::addIncludePath(JPATH_LIBRARIES . '/frenchconnections/tables');

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
              JRoute::_('index.php?option=' . $this->option . '&view=payment&layout=account&id=' . (int) $recordId, false)
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

    $profile = $this->getModel('Profile', 'UserModel');

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
   * 
   */
  public function approve()
  {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    // TO DO - Add permissions check here
    $model = $this->getModel('Property');

    $input = JFactory::getApplication()->input;
    
    $recordId = $input->get('id', '', 'int');


    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=review&layout=approve&id=' . $recordId, false
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
    $model->setState('com_realestate.listing.id', $recordId);
    $property_model = $this->getModel('Property');
    $listingreview_model = $this->getModel('Review');

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
    $model = $this->getModel('Property');
    $table = $model->getTable('Property');
    $cid = $this->input->post->get('cid', array(), 'array');
    $app = JFactory::getApplication();
    $context = "$this->option.edit.review";

    $recordId = (int) (count($cid) ? $cid[0] : $app->input->get('property_id', ''));
    $checkin = property_exists($table, 'checked_out');

    // Check user is authed to review
    if (!$user->authorise('realestate.listing.review', $this->option))
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
                      'index.php?option=' . $this->option . '&view=review&layout=property&id=' . $recordId, false
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
    $model = $this->getModel('Property');
    $table = $model->getTable('Property');
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

  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   *
   */

  public function view()
  {

    $context = "$this->option.edit.$this->context";
    $app = JFactory::getApplication();
    $model = $this->getModel('Property', $this->model_prefix);
    $table = $model->getTable();
    $user = JFactory::getUser();
    $isOwner = PropertyHelper::isOwner();
    $checkin = property_exists($table, 'checked_out');

    //  $id is the listing the user is trying to edit
    $id = $this->input->get('id', '', 'int');

    if (!$this->allowView($id))
    {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('You are not authorised to view this property listing at this time.', 'error');

      return false;
    }

    // Set the data in the users session context
    $app->setUserState($context . '.data', null);

    // Hold the edit ID once the id and user have been authorised.
    $this->holdEditId($context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false)
    );

    return true;
  }

}
