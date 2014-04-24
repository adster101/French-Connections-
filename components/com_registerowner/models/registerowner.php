<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class RegisterOwnerModelRegisterOwner extends JModelAdmin {

  /**
   * @var object item
   */
  protected $item;

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = '', $prefix = '', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the contact form.
   *
   * The base form is loaded from XML and then an event is fired
   *
   *
   * @param	array	$data		An optional array of data for the form to interrogate.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	JForm	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = false) {
    // Get the form.
    $form = $this->loadForm('com_registerowner.register', 'register', array('control' => 'jform', 'load_data' => true));
    if (empty($form)) {
      return false;
    }


    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_registerowner.register.data', array());

    if (empty($data)) {
      $data = array();
    }

    return $data;
  }

  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState() {

    return true;
  }

  /**
   * 
   * @param type $data
   * @return boolean
   */
  public function save($data) {
    $db = JFactory::getDBO();
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/tables');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/models');
    $message_data = array();

    try {

      // Get an db instance and start a transaction
      $db->transactionStart();

      $user = new JUser;

      $data['id'] = '';
      $data['groups'] = array('10');
      $data['email'] = $data['email1'];
      $data['registerDate'] = JFactory::getDate()->toSql();
      $data['name'] = $data['firstname'] . ' ' . $data['surname'];

      // Below should be parameterised so we can switch it off if we need to.
      $data['block'] = 0;

      // Bind the data.
      if (!$user->bind($data)) {
        $this->setError($user->getError());
        return false;
      }

      // Store the data.
      if (!$user->save()) {
        $this->setError($user->getError());
        return false;
      }

      // Also would be a good idea to insert a row into the profile db table

      $table = JTable::getInstance('UserProfileFc', 'RentalTable');

      // Set the table key to id so we ensure a new record is generated.
      // $table->set('_tbl_key', 'id');
      $table->set('_tbl_keys', array('id'));

      $user_profile['user_id'] = $user->id;
      $user_profile['firstname'] = $data['firstname'];
      $user_profile['surname'] = $data['surname'];
      $user_profile['phone_1'] = $data['dialling_code'] . ' ' . $data['phone_1'];

      if (!$table->save($user_profile)) {
        $this->setError($table->getError());
        Throw new Exception('Problem creating user profile');
      }

      // Get the menu based params 
      $params = $this->state->get('parameters.menu');

      // Get the config setting to set the email details for
      $config = JFactory::getConfig();
      $data['fromname'] = $config->get('fromname');
      $data['mailfrom'] = $params->get('email_from');
      $data['sitename'] = $config->get('sitename');

      $data['siteurl'] = JUri::root() . 'administrator';
      // Set the link to activate the user account.
      $uri = JUri::getInstance();

      //$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'] . '&advertiser=true', false);

      $emailSubject = JText::sprintf(
                      'COM_REGISTER_OWNER_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename'], $user->id
      );

      $emailBody = JText::sprintf(
                      'COM_REGISTEROWNER_EMAIL_REGISTERED_BODY', $data['name'], $data['sitename'], $data['username'], $user->password_clear, $user->id, $data['siteurl']
      );

      // Send the registration email. the true argument means it will go as HTML
      $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, true, $data['mailfrom']);

      if (!$return) {
        // Log out to file that email wasn't sent for what ever reason;
        // Trigger email to admin / office user. e.g. as per registration.php
      }

      // If we get here we're doing well.
      // While we're at it let's add an entry into the messages table so they will be greeted with
      // a lovingly hand crafted message of thanks for signing up.
      $message = $this->getTable('Message', 'MessagesTable');
      $message_data['user_id_from'] = 1;
      $message_data['user_id_to'] = $user->id;
      $message_data['subject'] = JText::_('COM_REGISTEROWNER_WELCOME_MESSAGE_SUBJECT');
      $message_data['message'] = JText::_('COM_REGISTEROWNER_WELCOME_MESSAGE_BODY');
      $message_data['date_time'] = JFactory::getDate()->calendar('Y-m-d H:i:s');
 
      if (!$message->save($message_data)) {
        $this->setError($table->getError());
      }
      
      $model = JModelLegacy::getInstance('Config','MessagesModel',$config=array('ignore_request'=>true));
      $model->setState('user.id', $user->id);
      if (!$model->save(array('auto_purge'=>0))) {

      }
      
      // Commit the transaction
      $db->transactionCommit();
      
      return true;
    } catch (Exception $e) {
      // Roll back any queries executed so far
      $db->transactionRollback();

      $this->setError($e->getMessage());
    }
  }

}
