<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
// jimport('joomla.application.component.controlleradmin');

// Include the utility class which extends controllerform
include_once('utility.php');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerListing extends HelloWorldControllerUtility {

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


  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   *
   */

  public function view() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // $id is the listing the user is trying to edit
    $id = $this->input->get('id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id')) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }


    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false)
    );
    return true;
  }


  public function renew()
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
}
