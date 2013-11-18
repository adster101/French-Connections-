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

  public function importunit() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    // Attributes list
    $attributes = array();

    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();
    $db->truncateTable('#__unit_attributes');

    $query = $db->getQuery(true);

    $query->select('id');
    $query->from('#__attributes');
    // Remove 8 from the list here as this attribute is stored against the property not the unit
    $query->where("attribute_type_id in (2,7,9,10,11,12,28)");

    // Set the query.
    $db->setQuery($query);

    // Do it, baby!
    $results = $db->loadObjectList();

    $query->clear();

    foreach ($results as $key => $value) {
      if ($value->id != 515 && $value->id != 616 && $value->id != 617) {
        $attributes[] = $value->id;
      }
    }

    while (($line = fgetcsv($handle, 0, "|")) !== FALSE) {

      // Initially we need to get the unit version id from the #__unit_versions table
      $query = $db->getQuery(true);

      $query->select('id');
      $query->from('#__unit_versions');
      $query->where('unit_id = ' . (int) $line[0]);

      // Set the query.
      $db->setQuery($query);

      // Do it, baby!
      $version_id = $db->loadRow();

      $query->clear();

      // The list of property attributes is a comma separated list so it is exploded to an array
      // The property type isn't listed in the slp_tax_id bit but is appended as the first entry in the list.
      $property_attributes = explode(',', $line[2]);
      $property_type = $property_attributes[1];

      $go = false;

      $property_id = $line[0];

      // Start building a new query to insert any attributes...
      $query = $db->getQuery(true);

      $query->insert('#__unit_attributes');

      $query->columns(array('version_id', 'property_id', 'attribute_id'));

      // Loop over the list of attributes for the property and check if each attribute is in the attributes list
      foreach ($property_attributes as $key => $value) {
        $insert_string = '';
        if (in_array($value, $attributes)) {
          $insert_string = "$version_id[0],$property_id,$value";
          $query->values($insert_string);
          $go = true;
        }
      }

      if (!empty($version_id[0])) {

        // Add the property type as well
        if ($go) {
          $insert_string = '';
          $insert_string = "$version_id[0],$property_id,$property_type";
          $query->values($insert_string);
        }

        // Set and execute the query
        $db->setQuery($query);
        if ($go) {
          if (!$db->execute()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
            print_r($db->getErrorMsg());
            print_r($insert_string);
            die;
          }
        }
      }
    }


    fclose($handle);

    $this->setMessage('Unit attributes imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=unitattributes');
  }

  public function importproperty() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    // Attributes list
    $attributes = array();

    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();
    $db->truncateTable('#__property_attributes');

    $query = $db->getQuery(true);

    $query->select('id');
    $query->from('#__attributes');
    // attribute types 8 and 
    $query->where("attribute_type_id in (8,28)");

    // Set the query.
    $db->setQuery($query);

    // Do it, baby!
    $results = $db->loadObjectList();

    $query->clear();

    foreach ($results as $key => $value) {
      if ($value->id != 515 && $value->id != 616 && $value->id != 617) {
        $attributes[] = $value->id;
      }
    }

    $previous_property_id = '';

    while (($line = fgetcsv($handle, 0, "|")) !== FALSE) {
      if ($previous_property_id == $line[1]) {
        continue;
      }
      // Initially we need to get the unit version id from the #__unit_versions table
      $query = $db->getQuery(true);

      $query->select('id');
      $query->from('#__property_versions');
      $query->where('property_id = ' . (int) $line[1]);

      // Set the query.
      $db->setQuery($query);

      // Do it, baby!
      $version_id = $db->loadRow();

      $query->clear();

      // The list of property attributes is a comma separated list so it is exploded to an array
      // The property type isn't listed in the slp_tax_id bit but is appended as the first entry in the list.
      $property_attributes = explode(',', $line[2]);

      $go = false;

      $property_id = $line[1];

      // Start building a new query to insert any attributes...
      $query = $db->getQuery(true);

      $query->insert('#__property_attributes');

      $query->columns(array('version_id', 'property_id', 'attribute_id'));

      // Loop over the list of attributes for the property and check if each attribute is in the attributes list
      foreach ($property_attributes as $key => $value) {
        $insert_string = '';
        if (in_array($value, $attributes)) {
          $insert_string = "$version_id[0],$property_id,$value";
          $query->values($insert_string);
          $go = true;
        }
      }

      if (!empty($version_id[0])) {
        
        // Set and execute the query
        $db->setQuery($query);
        if ($go) {
          if (!$db->execute()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
            print_r($db->getErrorMsg());
            print_r($insert_string);
            die;
          }
        }
      }

      $previous_property_id = $line[1];
    }


    fclose($handle);

    $this->setMessage('Unit attributes imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=propertyattributes');
  }

}
