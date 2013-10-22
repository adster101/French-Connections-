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



    while (($line = fgetcsv($handle,0,$delimiter='|')) !== FALSE) {

      // Get the nearest city/town based on the lat and long
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $latitude = ($line[10] > 0) ? $line[10] : '46.589069';
      $longitude = ($line[11] > 0) ? $line[11] : '2.416992';

      $query->select('id, title, level');
      $query->select(
              '(
        3959 * acos( cos( radians(' . $longitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians(' . $latitude . ') ) +
        sin( radians(' . $longitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');

      $query->from('#__classifications');
      $query->where('level = 5');

      $query->having('distance < 50');
      $query->order('distance asc');
      $db->setQuery($query, 0, 1);
      $items = $db->loadRow();

      $query->clear();
      
      // Do the same for airports
      $query->select('id');
      $query->select(
              '(
        3959 * acos( cos( radians(' . $longitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians(' . $latitude . ') ) +
        sin( radians(' . $longitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');

      $query->from('#__airports');

      $query->order('distance asc');
      $db->setQuery($query, 0, 1);
      $airport = $db->loadRow();
      

      $query->insert('#__property_versions');

      $query->columns(array(
          'property_id','title','country','area','region','department','city','location_details',
          'local_amenities','getting_there','airport','location_type','latitude','longitude',
          'distance_to_coast','video_url','booking_form','deposit_currency','security_currency',
          'deposit','security_deposit','payment_deadline','evening_meal','additional_booking_info',
          'terms_and_conditions','use_invoice_details','first_name','surname','address','phone_1',
          'phone_2','phone_3','fax','email_1','email_2','website','review','created_on','created_by',
          'modified_on','modified_by','published_on'
      ));
      $insert_string = '';

      // May need revising
      $insert_string .= $line[0];
      $insert_string .= ',' . $db->quote($line[1]);
      $insert_string .= ',' . 158052; // France
      $insert_string .= ',' . $line[2]; 
      $insert_string .= ',' . $line[3]; 
      $insert_string .= ',' . $line[4]; 
      $insert_string .= ',' . $db->quote($items[0]); 
      $insert_string .= ',' . $db->quote($line[5]); // Location details
      $insert_string .= ',' . $db->quote($line[6]); // Local amenities
      $insert_string .= ',' . $db->quote($line[7]); // Getting there
      $insert_string .= ',' . $db->quote($airport[0]); // Airport - sql this as per city
      $insert_string .= ',' . $db->quote($line[9]); // Location type
      $insert_string .= ',' . $line[10]; // Latitude
      $insert_string .= ',' . $db->quote($line[11]); // Longitude
      $insert_string .= ',' . $db->quote($line[12]); // Distance to coast
      $insert_string .= ',' . $db->quote($line[13]); // video url
      $insert_string .= ',' . $db->quote($line[14]); // Booking form
      $insert_string .= ',' . $db->quote($line[15]); // Deposit currency
      $insert_string .= ',' . $db->quote($line[16]); // security deposit currency
      $insert_string .= ',' . $db->quote($line[17]); // deposit
      $insert_string .= ',' . $db->quote($line[18]); // security deposit
      $insert_string .= ',' . $db->quote($line[19]); // payment deadline
      $insert_string .= ',' . $db->quote($line[20]); // evening meal
      $insert_string .= ',' . $db->quote($line[21]); // additional booking info
      $insert_string .= ',' . $db->quote($line[22]); // tandc
      $insert_string .= ',' . $db->quote($line[23]); // use invoice details
      $insert_string .= ',' . $db->quote($line[24]); 
      $insert_string .= ',' . $db->quote($line[25]);
      $insert_string .= ',' . $db->quote($line[26]);
      $insert_string .= ',' . $db->quote($line[27]);
      $insert_string .= ',' . $db->quote($line[28]);
      $insert_string .= ',' . $db->quote($line[29]);
      $insert_string .= ',' . $db->quote($line[30]);
      $insert_string .= ',' . $db->quote($line[31]);
      $insert_string .= ',' . $db->quote($line[32]);
      $insert_string .= ',' . $db->quote($line[33]); // website
      $insert_string .= ',0'; // Review     
      $insert_string .= ',' . $db->quote($line[34]); // date created
      $insert_string .= ',' . $db->quote($line[35]); // owner
      $insert_string .= ',' . $db->quote($line[36]); // modified
      $insert_string .= ',1'; // Modified by
      $insert_string .= ',' . $db->quote($line[37]); // published on

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

