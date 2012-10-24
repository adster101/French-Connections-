<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ClassificationControllerClassification extends JControllerForm {

  public function import() {

    $db = JFactory::getDbo();

    JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_classification/tables');

    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    $handle = fopen($userfile['tmp_name'], "r");

    $current_level = 1;
    $level_1_parent = 1;

    while (($line = fgetcsv($handle)) !== FALSE) {
      //$line is an array of the csv elements
      // Initialize a new category
      $instance = JTable::getInstance('ClassificationImport', 'ClassificationTable');
      $instance->title = $line[1];
      $instance->published = 1;
      $instance->access = 1;
      $instance->alias = JApplication::stringURLSafe($line[1]);
      $instance->latitude = $line[2];
      $instance->longitude = $line[3];
      
      $current_level = $line[0];

      if($current_level == 2) {
        $instance->parent_id = $level_2_parent_id;
      } elseif ($current_level == 3) {
        $instance->parent_id = $level_3_parent_id;
      } elseif ($current_level == 4) {
        $instance->parent_id = $level_4_parent_id;
      } else {
        $instance->parent_id = 1;
      }
      
      // Set the location in the tree
      $instance->setLocation($instance->parent_id, 'last-child');

      // Check to make sure our data is valid
      if (!$instance->check()) {
        JError::raiseNotice(500, $instance->getError());
        return false;
      }

      // Now store the category
      if (!$instance->store(true)) {
        JError::raiseNotice(500, $instance->getError());
        return false;
      }

      if (($current_level+1) == 2) {
        $level_2_parent_id = $instance->id;
      }
      if (($current_level+1) == 3) {
        $level_3_parent_id = $instance->id;
      }      
      if (($current_level+1) == 4) {
        $level_4_parent_id = $instance->id;
      }  
    }

    
    fclose($handle);


    $this->setRedirect('index.php?option=com_classification');
  }

  

}
