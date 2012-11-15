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
    JSession::checkToken( 'POST' ) or die( 'Invalid Token' );
    
    $config = JFactory::getConfig();

    
    // This is here as the user table instance checks that we aren't trying to insert a record with the same 
    // username as a super user. However, by default root_user is null. As we insert a load of dummy user to start 
    // with this is matched and the user thinks we are trying to replicate the root_user. We aren't and we 
    // explicity say there here by setting root_user in config.
    $config->set('root_user', 'admin');
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    $data = JRequest::getVar('jform', null, 'POST', 'array');
    $handle = fopen($userfile['tmp_name'], "r");

    JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_import/tables');
    
    $previous_property_id = '';
    
    while (($line = fgetcsv($handle)) !== FALSE) {

      // Insert a placeholder row for the user
      // Do this so we can set a primary key of our choice.
      // Otherwise, joomla insists on generating a new user id
      $db = JFactory::getDBO();

      $query = $db->getQuery(true);

      $query->insert('#__classifications');
      $query->columns(array('id','parent_id','created_by'));
      
      if ($previous_property_id == $line[1]){  
        $query->values("$line[0],$previous_property_id,$line[5]");
      } else {
        $query->values("$line[1],1,$line[5]");
      }
     
      $db->setQuery($query);

      if (!$db->execute())
      {
        echo "Problem inserting item into classifications table on locations import.";
        die;
      }
      
      // Get an instance of the helloworld table so we can update the placeholder we just inserted. 
      // May as well just insert it all above? No, below sorts the lft and rgt? Or is that just the call rebuild?
      $location = JTable::getInstance('ImportLocations', 'ImportTable');
      
      if ($previous_property_id == $line[1]) {
        $property->id = $line[0];
        $property->parent_id = $previous_property_id;       
      } else {
        $property->id = $line[1];
        $property->parent_id = 1;        
      }

      
      

      if(!$property->store()) {
        // Dump this out to a file?
        echo 'Property id: ' . $property->id . $property->getError();
        echo "<br />";
      }
      
      $previous_property_id = $line[1];
    }
    $property->rebuild();
          
    fclose($handle);
    
    $this->setMessage('Properties imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=properties');
  }

  

}
