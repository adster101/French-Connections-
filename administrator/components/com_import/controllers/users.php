<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.user.user');
jimport('joomla.user.helper');

/**
 * HelloWorld Controller
 */
class ImportControllerUsers extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();
    // This is here as the user table instance checks that we aren't trying to insert a record with the same 
    // username as a super user. However, by default root_user is null. As we insert a load of dummy user to start 
    // with this is matched and the user thinks we are trying to replicate the root_user. We aren't and we 
    // explicity say there here by setting root_user in config.
    $config->set('root_user', 'admin');
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    $data = JRequest::getVar('jform', null, 'POST', 'array');

    // Map the user to a group. Import only supports one group at the moment
    $groups = array();
    $groups[0] = $data['user_group'];

    $handle = fopen($userfile['tmp_name'], "r");

    // Import the JFormRule and JFormRuleEmail classes so we can validate email addresses
    jimport('joomla.form.rule');
    require_once(JPATH_SITE . '/libraries/joomla/form/rule/email.php');
    $isValidEmail = new JFormRuleEmail;

    // Create a new form element which JFormRuleEmail requires as an argument
    $element = new SimpleXMLElement('<field name="email" unique="false"></field>');

    // Create a log file for the email kickers
    jimport('joomla.error.log');

    JLog::addLogger(array('text_file' => 'user.import.php'), JLog::ALL, array('import_user'));

    $db = JFactory::getDBO();

    while (($line = fgetcsv($handle)) !== FALSE) {

      // If property owners group - harcoded, oh dear
      if ($data['user_group'] == 10) {
        // Insert a placeholder row for the user
        // Do this so we can set a primary key of our choice.
        // Otherwise, joomla insists on generating a new user id

        $query = $db->getQuery(true);

        $query->insert('#__users');
        $query->columns(array('id'));
        $query->values("$line[0]");

        $db->setQuery($query);

        if (!$db->execute()) {
          echo "woot!";
          die;
        }
      }


      // Get the salt and password details 
      $salt = JUserHelper::genRandomPassword(32);
      $password = JUserHelper::getCryptedPassword($line[4], $salt);

      // Create a JUser object
      $user = new JUser();

      // If this is going into the property owner group, set the user id to the existing value
      if ($data['user_group'] == 10) {
        $user->id = $line[0];
      }


      $user->groups = $groups;
      $user->name = $line[1];
      $user->username = $line[2];

      // Test that the email is at least in a valid format...
      if (!JMailHelper::isEmailAddress($line[3])) {
        $user->email = 'no_email_' . $user->id . '@email.com';
      } else {
        $user->email = $line[3];
      }
      $user->password = $array['password'] = $password . ':' . $salt;
      $user->block = $line[5];
      $user->sendEmail = $line[6];
      $user->registerDate = $line[7];
      $user->lastvisitDate = $line[8];
      $user->activation = $line[9];
      $user->params = $line[10];
      $user->lastResetTime = $line[11];

      if (!$user->save()) {
        // If this happens most likely user is a duplicate, log.
        JLog::add(JText::_($user->username . '(' . $user->id . ',' . $user->email . ').' . $user->getError()), JLog::WARNING, 'import_user');
      }
    }

    fclose($handle);

    $this->setMessage('Users imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=users');
  }

}
