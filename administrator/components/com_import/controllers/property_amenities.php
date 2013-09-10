<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ImportControllerProperty_amenities extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();

    // Create a log file for the email kickers
    jimport('joomla.error.log');

    JLog::addLogger(array('text_file' => 'property.amenities.import.php'), JLog::ALL, array('import_property_amenities'));

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');
    $table = JTable::getInstance('PropertyVersions', 'HelloWorldTable');
    
    

    // Get the nearest city/town based on the lat and long
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);



    $query->select('id');

    $query->from('#__property');


    $db->setQuery($query);
    $properties = $db->loadObjectList();

    foreach ($properties as $property) {

      $query = $db->getQuery(true);

      $query->select('*');
      $query->from('#__property_amenities');
      $query->where('property_id = ' . (int) $property->id);

      $db->setQuery($query);

      $amenities = $db->loadObjectList();

      if (count($amenities) > 0) {

        $tmp = array();

        $data['property_id'] = $property->id;

        foreach ($amenities as $amenity) {
          
          $tmp[$amenity->type_id]['type_id'] = $amenity->type_id; 
          $tmp[$amenity->type_id]['city'] = $amenity->city;
          $tmp[$amenity->type_id]['note'] = $amenity->notes;
          
        }
        // Sort on the type id
        sort($tmp);
        
        // Encode it to json and then updte the property id.
        $data['local_amenities'] = json_encode($tmp);
        
        $table->save($data);
      }
    }

    $this->setMessage('Amenities imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=property_amenities');
  }

}

