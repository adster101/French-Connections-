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
class ImportControllerLocations extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();


   
    $config->set('root_user', 'admin');
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    $handle = fopen($userfile['tmp_name'], "r");


    $current_level = 1;
    $level_1_parent_id = 1;


    while (($line = fgetcsv($handle)) !== FALSE) {
      // Insert a placeholder row for the user
      // Do this so we can set a primary key of our choice.
      // Otherwise, joomla insists on generating a new user id
      $db = JFactory::getDBO();

      $query = $db->getQuery(true);

      $query->insert('#__classifications');
      $query->columns(array('id', 'parent_id', 'title', 'description', 'path','alias', 'access', 'published', 'longitude', 'latitude'));

      $current_level = $line[1];

      if ($current_level == 2) {
        $parent_id = $level_2_parent_id;
      } elseif ($current_level == 3) {
        $parent_id = $level_3_parent_id;
      } elseif ($current_level == 4) {
        $parent_id = $level_4_parent_id;
      } else {
        $parent_id = 1;
      }

      $alias = JApplication::stringURLSafe($line[2]);
      $title = mysql_escape_string($line[2]);

      $query->values("$line[0],$parent_id,'$title','".  mysql_real_escape_string($line[5])."','$alias','$alias',1,1,$line[3],$line[4]");
     

      $db->setQuery($query);

      if (!$db->execute()) {
        echo "Problem inserting item into classifications table on locations import.";
        die;
      }
   
      if (($current_level+1) == 2) {
        $level_2_parent_id = $line[0];
      }
      if (($current_level+1) == 3) {
        $level_3_parent_id = $line[0];
      }      
      if (($current_level+1) == 4) {
        $level_4_parent_id = $line[0];
      }           
    }

    fclose($handle);
    
    JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_classification/tables');

    $classification = JTable::getInstance('Classification', 'ClassificationTable');
    
    $classification->rebuild();
    
    $this->setMessage('Properties imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=locations');
    
  }



}
