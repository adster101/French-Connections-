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
class ImportControllerProperty_listings extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();

    // Create a log file for the email kickers
    jimport('joomla.error.log');

    JLog::addLogger(array('text_file' => 'property.listings.import.php'), JLog::ALL, array('import_property_listings'));

    // This is here as the user table instance checks that we aren't trying to insert a record with the same 
    // username as a super user. However, by default root_user is null. As we insert a load of dummy user to start 
    // with this is matched and the user thinks we are trying to replicate the root_user. We aren't and we 
    // explicity say there here by setting root_user in config.
    $config->set('root_user', 'admin');
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    $handle = fopen($userfile['tmp_name'], "r");



    while (($line = fgetcsv($handle)) !== FALSE) {

      // Insert a placeholder row for the user
      // Do this so we can set a primary key of our choice.
      // Otherwise, joomla insists on generating a new user id
      $db = JFactory::getDBO();

      $query = $db->getQuery(true);

   

      // Get the nearest city/town based on the lat and long
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select('id, title, level');
      $query->select(
              '( 
        3959 * acos( cos( radians(' . $line[14] . ') ) 
        * cos( radians( latitude ) ) 
        * cos( radians( longitude ) - 
        radians(' . $line[13] . ') ) + 
        sin( radians(' . $line[14] . ') ) 
        * sin( radians( latitude ) ) ) ) 
AS distance            
            ');

      $query->from('#__classifications');
      $query->where('level = 4');

      $query->having('distance < 50');
      $query->order('distance');
      $db->setQuery($query, 0, 1);
      $items = $db->loadRow();
   
  
         
      $query->clear();
      
      $query->insert('#__property_listings');
   
      $query->columns(array(
          'id',
          'title',
          'area',
          'region',
          'department',
          'city',
          'created_by',
          'created_on',
          'modified',
          'expiry_date',
          'modified_by',
          'location_details',
          'getting_there',
          'location_type',
          'latitude',
          'longitude',
          'distance_to_coast',
          'published',
          'snooze_until'
          ));
      $insert_string = '';

      
      
      // May need revising 
      $insert_string .= $line[0];
      $insert_string .= ','.$db->quote($line[1]);
      $insert_string .= ','.$line[2];
      $insert_string .= ','.$line[3];
      $insert_string .= ','.$line[4];
      $insert_string .= ','.$db->quote($items[0]);
      $insert_string .= ','.$db->quote($line[5]);
      $insert_string .= ','.$db->quote($line[6]);
      $insert_string .= ','.$db->quote($line[7]);
      $insert_string .= ','.$db->quote($line[8]);
      $insert_string .= ','.$line[9];
      $insert_string .= ','.$db->quote($line[10]);
      $insert_string .= ','.$db->quote($line[11]);
      $insert_string .= ','.$db->quote($line[12]);
      $insert_string .= ','.$db->quote($line[13]);
      $insert_string .= ','.$db->quote($line[14]);
      $insert_string .= ','.$db->quote($line[15]);
      $insert_string .= ','.$line[16];
      $insert_string .= ','.$db->quote($line[17]);

      $query->values($insert_string);
      $db->setQuery($query);

      if (!$db->execute()) {
        $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
        print_r($db->getErrorMsg());
        print_r($insert_string);
        die;
      }
      
    }


      fclose($handle);

      $this->setMessage('Properties imported, hooray!');

      $this->setRedirect('index.php?option=com_import&view=property_listings');
    }
  }


  