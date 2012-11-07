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
class ImportControllerAttributes extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'POST' ) or die( 'Invalid Token' );
    
    // Attributes list
    $attributes = array();
    
    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');
    
    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();

    
    $query = $db->getQuery(true);

    $query->select('id');
    $query->from('#__attributes');
    $query->where("attribute_type_id in (8,9,10,11,12,28)");
    
    // Set the query.
		$db->setQuery($query);
    
    // Do it, baby!
		$results = $db->loadObjectList();

    $query->clear();
    
    foreach ($results as $key => $value) {
      $attributes[] = $value->id;
    }
        
    $previous_property_id = '';
      
    while (($line = fgetcsv($handle)) !== FALSE) {
      
      // The list of property attributes is a comma separated list so it is exploded to an array
      $property_attributes = explode(',',$line[2]);
      
      if ($previous_property_id == $line[1]) {
        $property_id = $line[0];
      } else {
        $property_id = $line[1];
      }

      // Start building a new query to insert any attributes... 
      $query = $db->getQuery(true);
      
      $query->insert('#__property_attributes');
      
			$query->columns(array('property_id','attribute_id'));
      
      // Loop over the list of attributes for the property and check if each attribute is in the attributes list
      foreach ($property_attributes as $key=>$value) {
        $insert_string = '';
        if (in_array($value, $attributes)) {
          $insert_string = "$property_id,$value";
          $query->values($insert_string);      
        }        

      }
      // Set and execute the query
			$db->setQuery($query);

      if (!$db->execute())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
				print_r($db->getErrorMsg());
				die;
			}
 
      
      $previous_property_id = $line[1];
    }
    
          
    fclose($handle);
    
    $this->setMessage('Properties imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=properties');
  }

  

}
