<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// Include the utility class which extends controllerform
//include_once('utility.php');

/**
 * HelloWorld Controller
 */
class RentalControllerListing extends JControllerForm {

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

    $this->registerTask('checkin', 'review');
  }

  /**
   * Method to check if you can View a record/resource.
   * TODO - Expand to check if listing is checked out to a user...
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowView($id = 0) {

    // Initialise variables.
    $user = JFactory::getUser();
    $userId = $user->get('id');
    $ownerId = '';

    // Check that this property is not checked out already
    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->extension)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension)) {
      // Now test the owner is the user.
      if (empty($ownerId) && $id) {
        // Need to do a lookup from the model.
        $record = $this->getModel('Property')->getItem($id);
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
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   *
   */

  public function view() {

    $context = "$this->option.edit.$this->context";
    $app = JFactory::getApplication();
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable();


    /**
     *  $id is the listing the user is trying to edit
     */
    $id = $this->input->get('id', '', 'int');

    if (!$this->allowView($id)) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('You are not authorised to view this property listing at this time.', 'error');

      return false;
    }

    $app->setUserState($context . '.data', null);

    // Check property out to user reviewing
    //if ($checkin && !$model->checkout($id)) {
    // Check-out failed, display a notice but allow the user to see the record.
    //$this->setError(JText::sprintf('This property is already checked out.', $model->getError()));
    //$this->setMessage($this->getError(), 'error');
    //$this->setRedirect(
    //JRoute::_(
    //'index.php?option=' . $this->option, false
    //)
    //);
    //return false;
    //}
    // Hold the edit ID once the id and user have been authorised.
    $this->holdEditId($context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false)
    );




    return true;
  }

  public function stats() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // Get the id of the property being statted
    $id = JFactory::getApplication()->input->getInt('id');

    if (!$this->allowView($id)) {

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

  public function accountupdate() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel('Payment');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.view.$this->context";

    // Get the record ID from the data array
    $recordId = $this->input->getInt('id', '', 'int');

    // Check that the edit ID is in the session scope
    if (!$this->checkEditId($context, $recordId)) {
      return false;
    }

    if (!$this->validate($model, $data, $context, $recordId)) {

      // Redirect back to the edit screen.
      $this->setRedirect(
              JRoute::_('index.php?option=com_rental&view=payment&layout=account&id=' . (int) $recordId, false)
      );
      return false;
    }

    // Need to do a lookup from the model.
    $record = $this->getModel('Property')->getItem($recordId);
    
    if (empty($record)) {
      return false;
    }

    $ownerId = $record->created_by;

    $data['user_id'] = $ownerId;

    $profile = $this->getModel('UserProfile');

    if (!$profile->save($data)) {
      return false;
    }

    $redirect = JRoute::_('index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId, false);
    $this->setRedirect($redirect, $message, 'warning');


    return true;
  }

  /**
   * Submit - controller to determine where to go when a listing in submitted for review
   *
   * @return boolean
   * 
   */
  public function submit() {
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    $app = JFactory::getApplication();
    $lang = JFactory::getLanguage();
    $model = $this->getModel('Submit');
    $data = $this->input->post->get('jform', array(), 'array');
    $context = "$this->option.view.$this->context";
    $task = $this->getTask();

    // Get the record ID from the data array
    $recordId = $this->input->post->get('property_id', '', 'int');

    // Check that the edit ID is in the session scope
    if (!$this->checkEditId($context, $recordId)) {
      return false;
    }

    // Validate the posted data.
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getForm($data, false);

    if (!$form) {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
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

    // Save the submittions note into the notes table, if there is a note.
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

    // Get the listing unit details
    $listing = $listing->getItems();


    $days_to_renewal = RentalHelper::getDaysToExpiry($listing[0]->expiry_date);
    var_dump($days_to_renewal);
    if (empty($listing[0]->vat_status)) { // No VAT status on record for this listing.
      $message = 'Oooh, naughty, you haven\'t told us about your VAT status';

      $redirect = JRoute::_('index.php?option=' . $this->extension . '&view=payment&layout=account&id=' . (int) $recordId, false);
    } elseif ($days_to_renewal < 7 && $days_to_renewal > 0) {
      // If there are less than seven days to renewal or is a new property listing (e.g. doesn't have an expiry date)
      $message = ($days_to_renewal > 0) ? 'Your property is expiring within 7 days - please renew now' : 'Property expired, renew now.';

      $redirect = JRoute::_('index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId, false);
    } else if (empty($days_to_renewal)) {

      $message = 'Oh, looks like you\'re submitting a new property. Submitted for review, etc etc ';

      $redirect = JRoute::_('index.php?option=' . $this->extension . '&view=payment&id=' . (int) $recordId, false);
    } else {

      // Need to determine whether they owe us any more wedge
      $model = $this->getModel('Property', 'RentalModel', $config = array('ignore_request' => true));

      $model->updateProperty($listing_id = $listing[0]->id, 2);

      $redirect = JRoute::_('index.php?option=' . $this->extension, false);
    }


    $this->setRedirect($redirect, $message, 'warning');
  }

  /**
   * review controller action - handles the case when a user wants to review the changes to a listing.
   * 
   * 
   */
  public function review() {

    // Get the user
    $user = JFactory::getUser();
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable('Property', 'RentalTable');
    $cid = $this->input->post->get('cid', array(), 'array');
    $app = JFactory::getApplication();
    $context = "$this->option.review.$this->context";

    $recordId = (int) (count($cid) ? $cid[0] : 0);
    $checkin = property_exists($table, 'checked_out');

    // Check user is authed to review
    if (!$user->authorise('helloworld.property.review', $this->option)) {

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
    if ($checkin && !$model->checkout($recordId)) {
      // Check-out failed, display a notice but allow the user to see the record.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false
              )
      );

      return false;
    } else {
      // Check-out succeeded, push the new record id into the session.
      $this->holdEditId($context, $recordId);
      $app->setUserState($context . '.data', null);

      $this->view_item = 'propertyversions';

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=listingreview&layout=property&property_id=' . $recordId, false
              )
      );

      return true;
    }
  }

  /**
   * 
   * @return boolean
   */
  public function release() {

    // Get the user
    $user = JFactory::getUser();
    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable('Property', 'RentalTable');
    $input = JFactory::getApplication()->input;

    $recordId = $input->get('id', '', 'int');

    $checkin = property_exists($table, 'checked_out');

    // TO DO - CHECK Edit id is in the session for this user
    // Check-in the original row.
    if ($checkin && $model->checkin($recordId) === false) {
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

  /**
   * 
   */
  public function approve() {

    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable();

    $input = JFactory::getApplication()->input;
    $recordId = $input->get('id', '', 'int');
    $checkin = property_exists($table, 'checked_out');

    $recordId = $input->get('id', '', 'int');

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listingreview&layout=approve&property_id=' . $recordId, false
            )
    );
    return true;
  }

  /**
   * 
   */
  public function publish() {

    $model = $this->getModel('Property', 'RentalModel');
    $table = $model->getTable();

    $input = JFactory::getApplication()->input;
    $recordId = $input->get('id', '', 'int');
    $checkin = property_exists($table, 'checked_out');

    $recordId = $input->get('id', '', 'int');

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listingreview&layout=approve&property_id=' . $recordId, false
            )
    );
    return true;
  }

  /**
   * Overriden checkEditId so it's usable by multiple actions
   * @param type $context
   * @param type $id
   * @return boolean
   */
  public function checkEditId($context, $id) {
    if (!parent::checkEditId($context, $id)) {
      // Somehow the person just went to the form and tried to save it. We don't allow that.
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
    return true;
  }

  public function validate($model, $data, $context, $recordId) {

    $app = JFactory::getApplication();

    // Validate the posted data.
    // Sometimes the form needs some posted data, such as for plugins and modules.
    $form = $model->getForm($data, false);

    if (!$form) {
      $app->enqueueMessage($model->getError(), 'error');

      return false;
    }

    // Test whether the data is valid.
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

      $app->setUserState($context . '.data', $data);



      return false;
    }
    return true;
  }

}
