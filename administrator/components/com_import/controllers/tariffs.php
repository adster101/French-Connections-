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
class ImportControllerTariffs extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'POST' ) or die( 'Invalid Token' );
    
    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    
    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();
    $db->truncateTable('#__tariffs');
    
    $previous_property_id = '';
    $previous_unit_id = '';
    
    while (($line = fgetcsv($handle)) !== FALSE) {

     
      // Determine the tariff based on the rate per, this should match to the imported property data...
      if ($line[5] == 'night') {
        $tariff = $line[3];
      } else {
        $tariff = $line[4];
      }

      // Start building a new query to insert any attributes... 
      $query = $db->getQuery(true);
      
      $query->insert('#__tariffs');
      
			$query->columns(array('unit_id','start_date','end_date','tariff'));
      
      // Loop over the list of attributes for the property and check if each attribute is in the attributes list
      $insert_string = '';
      
      $insert_string = "$line[0],'$line[1]','$line[2]',$tariff";
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
    $this->setRedirect('index.php?option=com_import&view=tariffs');
  }
}
