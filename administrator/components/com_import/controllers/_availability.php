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
class ImportControllerAvailability extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'POST' ) or die( 'Invalid Token' );
    
    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    
    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();
    
    $previous_property_id = '';
    $previous_unit_id = '';
    $unit_count = 1; // All imported properties must have at least one unit of reference  
    
    while (($line = fgetcsv($handle)) !== FALSE) {

      // This is another line of availability for the same unit and property
      if ($previous_property_id == $line[1] && $previous_unit_id == $line[0]) {
        
        // If unit count is one, we must be dealing with the first unit of the property...
        if ($unit_count == 1) {
          // So set the property ID accordingly. Caveat here is that prn becomes the parent property id for co located properties.
          $property_id = $line[1];
        } else {
          $property_id = $line[0];
        }
        
      } else if ($previous_property_id == $line[1] && $previous_unit_id != $line[0]) {
        
        // Must be a new unit of the same property, so we also increment the unit count (as we know this is a multi unit property)
        $property_id = $line[0];
        $unit_count++;
        
      } else {

        // Only happens when we deal with a new property/unit combo
        $property_id = $line[1];
        $unit_count = 1; // reset the unit count as this must be a new prn
      }

      // Start building a new query to insert any attributes... 
      $query = $db->getQuery(true);
      
      $query->insert('#__availability');
      
			$query->columns(array('unit_id','start_date','end_date','availability'));
      
      // Loop over the list of attributes for the property and check if each attribute is in the attributes list
      $insert_string = '';
      
      $insert_string = "$property_id,'$line[2]','$line[3]',1";
      $query->values($insert_string);      
               
      // Set and execute the query
			$db->setQuery($query);

      if (!$db->execute())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
				print_r($db->getErrorMsg());
        print_r($insert_string);
				die;
			}
 
      $previous_unit_id = $line[0];
      
      $previous_property_id = $line[1];
    }
    
          
    fclose($handle);
    
    $this->setMessage('Properties imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=availability');
  }

  

}
