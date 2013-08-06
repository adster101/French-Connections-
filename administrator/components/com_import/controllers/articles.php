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
class ImportControllerArticles extends JControllerForm {

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

    // Add the content model
    JControllerForm::addModelPath(JPATH_ADMINISTRATOR . '/components/com_content/models');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    while (($line = fgetcsv($handle, 0, $delimiter = '|')) !== FALSE) {
      $data = array();

      $model = $this->getModel('Article', 'ContentModel');
      
      $output = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $line[9]);
      
      $data['fulltext'] = $output; 
      $data['id'] = '';
      $data['state'] = ($line[1] == 'True') ? 1 : 0;
      $data['title'] = $line[7];
      $data['created'] = $line[2];
      $data['catid'] = 83;
      $data['language'] = 'en-GB';
      $data['metadesc'] = $line[12];
      $data['metakey'] = $line[13];
            
      $data['published_up'] = date('Y-m-d', strtotime($line[14]));
      $data['published_down'] = date('Y-m-d', strtotime($line[15]));
      
      if (!$model->save($data)) {
        $error = $model->getError();
      }     
      

    } 


    fclose($handle);

    $this->setMessage('Articles imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=articles');
  }

}
