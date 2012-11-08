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
class ImportControllerProperties extends JControllerForm {

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

      $query->insert('#__helloworld');
      $query->columns(array('id','parent_id','created_by'));
      
      if ($previous_property_id == $line[1]){  
        $query->values("$line[0],$previous_property_id,$line[5]");
      } else {
        $query->values("$line[1],1,$line[5]");
      }
     
      $db->setQuery($query);

      if (!$db->execute())
      {
        echo "Problem inserting item into helloworld table on property import.";
        die;
      }
      
      // Get an instance of the helloworld table so we can update the placeholder we just inserted. 
      // May as well just insert it all above? No, below sorts the lft and rgt? Or is that just the call rebuild?
      $property = JTable::getInstance('ImportProperty', 'ImportTable');
      
      if ($previous_property_id == $line[1]) {
        $property->id = $line[0];
        $property->parent_id = $previous_property_id;       
      } else {
        $property->id = $line[1];
        $property->parent_id = 1;        
      }

      // May need revising 
      $property->greeting = $line[2];
      $property->catid = $line[3];
      $property->params = $line[4];
      $property->created_on = $line[6];
      $property->modified = $line[7];
      $property->expiry_date = $line[8];
      $property->modified_by = $line[9];
      $property->lang = $line[10];
      $property->description = $line[11];
      $property->thumbnail = $line[12];
      $property->occupancy = $line[13];
      $property->single_bedrooms = $line[14];
      $property->double_bedrooms = $line[15];
      $property->triple_bedrooms = $line[16];
      $property->quad_bedrooms = $line[17];
      $property->twin_bedrooms = $line[18];
      $property->childrens_beds = $line[19];
      $property->cots = $line[20];
      $property->extra_beds = $line[21];
      $property->bathrooms = $line[22];
      $property->toilets = $line[23];
      $property->swimming = $line[24];
      $property->accommodation_type = $line[25];
      $property->property_type = $line[26];
      $property->location_type = $line[27];
      $property->latitude = $line[28];
      $property->longitude = $line[29];
      $property->nearest_town = $line[30];
      $property->distance_to_coast = $line[31];
      $property->additional_price_notes = $line[32];
      $property->base_currency = $line[33];
      $property->tariff_based_on = $line[34];
      $property->linen_costs = $line[35];
      $property->changeover_day = $line[36];
      $property->published = $line[37];
      $property->video = $line[38];
      

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
