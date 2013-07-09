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
      
      // If column 8 is not empty then we append that to the full text as an h2.
      if (!empty($line[8])) {
        $data['fulltext'] = '<h2>' . $line[8] . '</h2>' . $line[10];
      } else {
        $data['fulltext'] = $line[10];
      }

      $data['id'] = '';
      $data['state'] = $line[1];
      $data['introtext'] = $line[8];
      $data['title'] = $line[7];
      $data['created'] = $line[2];
      $data['catid'] = 70;
      $data['language'] = 'en-GB';
      $data['metakey'] = $line[14];
      $data['metadesc'] = $line[13];



      if (!$model->save($data)) {
        $error = $model->getError();
      }     
  
    }

    fclose($handle);

    $this->setMessage('Articles imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=articles');
  }

}
