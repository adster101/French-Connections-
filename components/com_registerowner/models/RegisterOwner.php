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


    $app = JFactory::getApplication();

    $input = $app->input;

    $request = $input->request;

    // Get the message id
    $id = $input->get('id', '', 'int');

    $this->setState('property.id', $id);
  }

  /**
   * 
   * @param type $data
   * @return boolean
   */
  public function save($data) {

		$user = new JUser;

    $data['id'] = '';
    $data['groups'] = array('10');
    $data['email'] = $data['email1'];
    $data['registerDate'] = JFactory::getDate()->toSql();

    // Below should be parameterised so we can switch it off if we need to.
    $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
    $data['block'] = 1;

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

    // Errr, also update the phone number, a lot of hassle if you can't do that, innit!
    
    // Get the config setting to set the email details for
    $config = JFactory::getConfig();
    $data['fromname'] = $config->get('fromname');
    $data['mailfrom'] = $config->get('mailfrom');
    $data['sitename'] = $config->get('sitename');

    $data['siteurl'] = JUri::root() . 'administrator';
    // Set the link to activate the user account.
    $uri = JUri::getInstance();
    $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
    $data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

    $emailSubject = JText::sprintf(
                    'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
    );

    $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY', $data['name'], $data['sitename'], $data['activate'], $data['siteurl'], $data['username'], $user->password_clear
    );

    // Send the registration email.
    $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
    
    if (!$return) {
      return false;
    }
    
    return true;
    
  }

}
