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
class ImportUsersControllerImportUsers extends JControllerForm {

  public function import() {
  
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'POST' ) or die( 'Invalid Token' );
    
    
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    $data = JRequest::getVar('jform', null, 'POST', 'array');

    $groups = array();
    $groups[0] = $data['user_group'];
    
    $handle = fopen($userfile['tmp_name'], "r");
    
    $usermap = array();
    
    while (($line = fgetcsv($handle)) !== FALSE) {
      $user = new JUser();
      $user->groups = $groups;
      $user->name = $line[1];
      $user->username = $line[2];
      $user->email = $line[3];
      $user->password = JUserHelper::getCryptedPassword($line[4]);
      $user->block = $line[5];
      $user->sendEmail = $line[6];
      $user->registerDate = $line[7];
      $user->lastvisitDate = $line[8];
      $user->activation = $line[9];
      $user->params = $line[10];
      $user->lastResetTime = $line[11];

      
      if(!$user->save()) {
        print_r($user->getError());
        die;
      }
      
      $usermap[$line[0]] = $user->id; 
      
    }
    print_r($usermap);die;
    fclose($handle);

    $this->setRedirect('index.php?option=com_importusers');
  }

  

}
