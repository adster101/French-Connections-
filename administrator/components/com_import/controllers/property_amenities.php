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

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    $table = JTable::getInstance('PropertyVersions', 'RentalTable');

    $lang = JFactory::getLanguage();

    // Get the nearest city/town based on the lat and long
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);



    $query->select('id');

    $query->from('#__property');

    $key = array('1' => 'amenity_bakery', '2' => 'amenity_bar', '3' => 'amenity_market', '4' => 'amenity_pharmacy', '5' => 'amenity_supermarket', '6' => 'amenity_tourist');

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

        $tmp = array('amenity_bakery' => '', 'amenity_bar' => '', 'amenity_market' => '', 'amenity_pharmacy' => '', 'amenity_supermarket' => '', 'amenity_tourist' => '');

        $data['property_id'] = $property->id;

        foreach ($amenities as $amenity) {

          // Get the name of the town from the classification table
          $query->clear();
          $query->select('title');
          $query->from('#__classifications');
          $query->where('id = ' . $amenity->city);
          $db->setQuery($query);
          $result = $db->loadRow();

          // Replace accented characters with utf8 safe equivalents
          $town = $lang->transliterate($result[0]);


          // Get the notes field, prepending a dash if not empty
          $notes = ($amenity->notes) ? ' - ' . $amenity->notes : '';

          // Concat the town and notes and assign it to the relevent key
          $tmp[$key[$amenity->type_id]] = $town . $notes;
        }

        // Encode it to json and then updte the property id.
        $data['local_amenities'] = json_encode($tmp);

        $table->save($data);
      }
    }

    $this->setMessage('Amenities imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=property_amenities');
  }

}

