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
      //$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
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
      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components//com_helloworld/tables');
      $table = JTable::getInstance('UserProfileFc', 'HelloWorldTable');
      
      // Set the table key to id so we ensure a new record is generated.
      $table->set('_tbl_key', 'id');

      $user_profile['user_id'] = $user->id;
      $user_profile['firstname'] = $data['firstname'];
      $user_profile['surname'] = $data['surname'];
      $user_profile['phone_1'] = $data['phone_1'];

      if (!$table->save($user_profile)) {
        $this->setError($table->getError());
        Throw new Exception('Problem creating user profile');
      }

      // Commit the transaction
      $db->transactionCommit();

      // Get the config setting to set the email details for
      $config = JFactory::getConfig();
      $data['fromname'] = $config->get('fromname');
      $data['mailfrom'] = $config->get('mailfrom');
      $data['sitename'] = $config->get('sitename');

      $data['siteurl'] = JUri::root() . 'administrator';
      // Set the link to activate the user account.
      $uri = JUri::getInstance();
      
      $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
      //$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'] . '&advertiser=true', false);

      $emailSubject = JText::sprintf(
                      'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
      );

      $emailBody = JText::sprintf(
                      'COM_REGISTEROWNER_EMAIL_REGISTERED_BODY', $data['name'], $data['sitename'],$data['siteurl'], $data['siteurl'], $data['username'], $user->password_clear
      );

      // Send the registration email. the true argument means it will go as HTML
      $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, true);

      if (!$return) {
        return false;
      }

      return true;
    } catch (Exception $e) {

      // Roll back any queries executed so far
      $db->transactionRollback();

      $this->setError($e->getMessage());
    }
  }

}
