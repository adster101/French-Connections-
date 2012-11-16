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
      $property->title = $line[2];
      $property->catid = $line[3];
      $property->params = $line[4];
      $property->created_on = $line[6];
      $property->modified = $line[7];
      $property->expiry_date = $line[8];
      $property->modified_by = $line[9];
      $property->lang = $line[10];
      $property->description = $line[11].$line[12].$line[13];
      $property->internal_facilities_other = $line[14];
      $property->external_facilities_other = $line[15];
      $property->activities_other = $line[16];
      $property->location_details = $line[17];
      $property->getting_there = $line[18];
      $property->thumbnail = $line[19];
      $property->occupancy = $line[20];
      $property->single_bedrooms = $line[21];
      $property->double_bedrooms = $line[22];
      $property->triple_bedrooms = $line[23];
      $property->quad_bedrooms = $line[24];
      $property->twin_bedrooms = $line[25];
      $property->childrens_beds = $line[26];
      $property->cots = $line[27];
      $property->extra_beds = $line[28];
      $property->bathrooms = $line[29];
      $property->toilets = $line[30];
      $property->swimming = $line[31];
      $property->accommodation_type = $line[32];
      $property->property_type = $line[33];
      $property->location_type = $line[34];
      $property->latitude = $line[35];
      $property->longitude = $line[36];
      $property->nearest_town = $line[37];
      $property->distance_to_coast = $line[38];
      $property->additional_price_notes = $line[39];
      $property->base_currency = $line[40];
      $property->tariff_based_on = $line[41];
      $property->linen_costs = $line[42];
      $property->changeover_day = $line[43];
      $property->published = $line[44];
      $property->video = $line[45];
      

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
