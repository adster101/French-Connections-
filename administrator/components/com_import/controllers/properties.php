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
    
    // Create a log file for the email kickers
    jimport('joomla.error.log');

    JLog::addLogger(array('text_file' => 'images.import.php'), JLog::ALL, array('import_images'));
    
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
        $query->values("$line[0],$previous_property_id,$line[7]");
      } else {
        $query->values("$line[1],1,$line[7]");
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
      $property->area = $line[3];
      $property->region = $line[4];
      $property->department = $line[5];
      $property->params = $line[6];
      $property->created_on = $line[8];
      $property->modified = $line[9];
      $property->expiry_date = $line[10];
      $property->modified_by = $line[11];
      $property->lang = $line[12];
      $property->description = $line[13].$line[14].$line[15];
      $property->internal_facilities_other = $line[16];
      $property->external_facilities_other = $line[17];
      $property->activities_other = $line[18];
      $property->location_details = $line[19];
      $property->getting_there = $line[20];
      $property->thumbnail = $line[21];
      $property->occupancy = $line[22];
      $property->single_bedrooms = $line[23];
      $property->double_bedrooms = $line[24];
      $property->triple_bedrooms = $line[25];
      $property->quad_bedrooms = $line[26];
      $property->twin_bedrooms = $line[27];
      $property->childrens_beds = $line[28];
      $property->cots = $line[29];
      $property->extra_beds = $line[30];
      $property->bathrooms = $line[31];
      $property->toilets = $line[32];
      $property->swimming = $line[33];
      $property->accommodation_type = $line[34];
      $property->property_type = $line[35];
      $property->location_type = $line[36];
      $property->latitude = $line[37];
      $property->longitude = $line[38];
      $property->nearest_town = $line[39];
      $property->distance_to_coast = $line[40];
      $property->additional_price_notes = $line[41];
      $property->base_currency = $line[42];
      $property->tariff_based_on = $line[43];
      $property->linen_costs = $line[44];
      $property->changeover_day = $line[45];
      $property->published = $line[46];
      $property->video = $line[47];
      

      if(!$property->store()) {
        // Dump this out to a file?
        echo 'Property id: ' . $property->id . $property->getError();
        echo "<br />";
      }
      
      $previous_property_id = $line[1];
      $folder = JPATH_ROOT . '/' . 'images';

   
      
      // Take a copy of the original image and generate the property thumbnail at the same time....
      if (!file_exists($folder . '/' . $previous_property_id . '/' . $property->thumbnail)) {
        
        $move = copy('D:\\\Pics/_images/' . $property->thumbnail, $folder . '/' . $previous_property_id . '/' . $property->thumbnail);

        if (!$move) {
          JLog::add('Unable to move/locate image - ' . $property->thumbnail . '(' . $image['id'] . ')', JLog::ERROR, 'import_images');
        }

        
      }      
      
      if (file_exists($folder . '/' . $previous_property_id . '/' . $property->thumbnail)) {
          try {
            $imgObj = new JImage($folder . '/' . $previous_property_id . '/' . $property->thumbnail);
          } catch (Exception $e) {

            JLog::add('Cannot move image (wrong mime type?) - ' . $property->thumbnail . '(' . $image['id'] . ')', JLog::ERROR, 'import_images');
          }

          // Consider making this not a crop but one of the other image preparation types to prevent the loss of detail?  
          $imgObj->createThumbs('210x120', 1, $folder . '/' . $previous_property_id . '/thumb/');
        }
    }
    
    $property->rebuild();
          
    fclose($handle);
    
    $this->setMessage('Properties imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=properties');
  }

  

}
