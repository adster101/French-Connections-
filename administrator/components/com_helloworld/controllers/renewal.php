<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerRenewal extends JControllerForm {

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
  
  
  public function process ()
  {

   // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');
    
    $app = JFactory::getApplication();

    $records = $this->input->get('cid', array(), 'array');
    $recordId = $records[0];
    $model = $this->getModel('Property','HelloWorldModel');
    $table = $model->getTable();
 		$context = "$this->option.property.$this->context";
   
    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    // Check that the owner/user can edit/renew this record
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

    // Get the details of the property listing that is being renewed
    $record = $model->getItem($recordId);

    // Check the review status and the expiry date
    if ($record->review == 1) {

      // OOps property needs to be submitted for review before it can be renewed.
      // If status is anything but 1 then okay, we can let it through
      // i.e. 0 is okay, 2 is locked for editing, e.g. marked for review
      // Redirect to com_helloworld&task=property.renew
        // Redirect to the renewal payment/summary form thingy...
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->extension . '&view=properties', false
                )
        );      
    } else {

      // Assume that expiry date is in the past...
      if ($record->expiry_date) {

        // Add this to holdEditId etc...and then redirect to the view directly
        JApplication::setUserState($this->extension . '.listing.detail', $record);
        
        // Check-out succeeded, push the new record id into the session.
        $this->holdEditId($context, $recordId);
        $app->setUserState($context . '.data', null);        
       
        // Redirect to the renewal payment/summary form thingy...
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->extension . '&view=renewal&layout=billing&id=' . (int) $recordId, false
                )
        );
      }
    }
 

    return false;
  }  
  
  /*
   * 
   * Overriden save method
   * 
   */
  public function doPayment($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $this->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.property.$this->context";
		$task = $this->getTask();
    

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}
    
		$recordId = $this->input->getInt($urlVar);
    

		if (!$this->checkEditId($context, $recordId))
		{
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

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}    
    
    // Here we have valid address and VAT status data...
    // 
    // Forwards on to the money page...
    
    
    
    
  }
}
