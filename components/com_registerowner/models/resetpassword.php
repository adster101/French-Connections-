<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class RegisterOwnerModelResetPassword extends JModelAdmin
{

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
  public function getTable($type = '', $prefix = '', $config = array())
  {
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
  public function getForm($data = array(), $loadData = false)
  {
    // Get the form.
    $form = $this->loadForm('com_registerowner.resetpassword', 'resetpassword', array('control' => 'jform', 'load_data' => true));

    if (empty($form))
    {
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
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_registerowner.resetpassword.data', array());

    if (empty($data))
    {
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
  protected function populateState()
  {
    return true;
  }

  /**
   * 
   * @param type $data
   * @return boolean
   */
  public function save($data)
  {

    $db = JFactory::getDBO();

    // Based on the email supplied look up a user ID from the email
    $userid = $this->getUserID($data['email']);
    $user = JFactory::getUser($userid);
    try
    {

      // Get an db instance and start a transaction
      $db->transactionStart();
      $password = JUserHelper::genRandomPassword();
      $data['password'] = JUserHelper::hashPassword($password);

      // Bind the data.
      if (!$user->setProperties($data))
      {
        $this->setError($user->getError());
        return false;
      }

      // Store the data.
      if (!$user->save())
      {
        $this->setError($user->getError());
        return false;
      }

      // Get the menu based params 
      $params = $this->state->get('parameters.menu');
      // TO DO - Tidy this shit up!
      // Get the config setting to set the email details for
      $config = JFactory::getConfig();
      $data['fromname'] = $config->get('fromname');
      $data['mailfrom'] = $config->get('mailfrom');
      $data['sitename'] = $config->get('sitename');

      // The admin login url
      $data['siteurl'] = JUri::root() . 'administrator';
      

      $emailSubject = JText::sprintf(
                      'COM_REGISTEROWNER_PASSWORD_RESET_EMAIL_SUBJECT', $data['sitename']
      );

      $emailBody = JText::sprintf(
                      'COM_REGISTEROWNER_PASSWORD_RESET_EMAIL_BODY', $user->name, $data['sitename'], $password
      );

      $recipient = (JDEBUG) ? $params->get('email_from') : $user->email;
      
      // Send the registration email. the true argument means it will go as HTML
      $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $recipient, $emailSubject, $emailBody, true);

      if (!$return)
      {
        // Log out to file that email wasn't sent for what ever reason;
        // Trigger email to admin / office user. e.g. as per registration.php
        Throw new Exception('Problem creating user profile');
      }

      // Commit the transaction
      $db->transactionCommit();

      // Return the user object we've just created
      return $user;
    }
    catch (Exception $e)
    {

      // Roll back any queries executed so far
      $db->transactionRollback();
      $this->setError($e->getMessage());

      return false;
    }
  }

  public function getUserID($email = '')
  {
    // Get the database object and a new query object.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    // Build the query.
    $query->select('id')
            ->from('#__users')
            ->where('email = ' . $db->quote($email));

    // Set and query the database.
    $db->setQuery($query);
    $id = $db->loadResult();

    if (!$id)
    {
      return false;
    }
    
    return $id;
  }

}
