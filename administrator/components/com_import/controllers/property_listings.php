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

  function cp1252_to_utf8($str) {
    global $cp1252_map;
    return strtr(utf8_encode($str), $cp1252_map);
  }

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();
    $cp1252_map = array(
        "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
        "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
        "\xc2\x83" => "\xc6\x92", /* LATIN SMALL LETTER F WITH HOOK */
        "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
        "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
        "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
        "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
        "\xc2\x88" => "\xcb\x86", /* MODIFIER LETTER CIRCUMFLEX ACCENT */
        "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
        "\xc2\x8a" => "\xc5\xa0", /* LATIN CAPITAL LETTER S WITH CARON */
        "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
        "\xc2\x8c" => "\xc5\x92", /* LATIN CAPITAL LIGATURE OE */
        "\xc2\x8e" => "\xc5\xbd", /* LATIN CAPITAL LETTER Z WITH CARON */
        "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
        "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
        "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
        "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
        "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
        "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
        "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */
        "\xc2\x98" => "\xcb\x9c", /* SMALL TILDE */
        "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
        "\xc2\x9a" => "\xc5\xa1", /* LATIN SMALL LETTER S WITH CARON */
        "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION */
        "\xc2\x9c" => "\xc5\x93", /* LATIN SMALL LIGATURE OE */
        "\xc2\x9e" => "\xc5\xbe", /* LATIN SMALL LETTER Z WITH CARON */
        "\xc2\x9f" => "\xc5\xb8" /* LATIN CAPITAL LETTER Y WITH DIAERESIS */
    );



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
    $langs = array(
        1 => 'LANGUAGE_ENGLISH',
        2 => 'LANGUAGE_FRENCH',
        3 => 'LANGUAGE_ITALIAN',
        4 => 'LANGUAGE_GERMAN',
        5 => 'LANGUAGE_SPANISH',
        6 => 'LANGUAGE_DUTCH',
        7 => 'LANGUAGE_GREEK',
        8 => 'LANGUAGE_POLISH',
        9 => 'LANGUAGE_HUNGARIAN',
        10 => 'LANGUAGE_PORTUGESE',
        11 => 'LANGUAGE_RUSSIAN',
        12 => 'LANGUAGE_WELSH',
        13 => 'LANGUAGE_DANISH',
        14 => 'LANGUAGE_CZECH',
        15 => 'LANGUAGE_NORWEGIAN',
        16 => 'LANGUAGE_SWEDISH',
        17 => 'LANGUAGE_IRISH',
        18 => 'LANGUAGE_JAPANESE',
        19 => 'LANGUAGE_FINNISH',
        20 => 'LANGUAGE_CHINESE',
        21 => 'LANGUAGE_CATALAN'
    );

    $registry = new JRegistry;
    // Get the nearest city/town based on the lat and long
    $db = JFactory::getDbo();
    $db->truncateTable('#__property_versions');

    while (($line = fgetcsv($handle, 0, $delimiter = '|')) !== FALSE) {

      
      $query = $db->getQuery(true);
      $latitude = ($line[11]) ? $line[11] : '46.589069';
      $longitude = ($line[12]) ? $line[12] : '2.416992';

      if (empty($line[5])) { // If the nearest town hasn't been picked up for whatever reason, try and guess it
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
      } else { // Nearest town as per the TMG CMS
        $items[0] = $line[5];
      }



      // Do the same for airports
      if (empty($line[9])) {
        $query->select('id');
        $query->select(
                '(
        3959 * acos( cos( radians(' . $latitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians(' . $longitude . ') ) +
        sin( radians(' . $latitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');

        $query->from('#__airports');

        $query->order('distance asc');
      } else { // Look up the airport from the airport table.
        $query->select('id');
        $query->from('#__airports');
        $query->where('existing_id = ' . (int) $line[9]);
      }

      $db->setQuery($query, 0, 1);
      $airport = $db->loadRow();

      $languages_spoken = array_unique(explode(',', $line[40]));

      $languages_spoken_array['language_1'] = $langs[$languages_spoken[0]];
      $languages_spoken_array['language_2'] = (array_key_exists(1, $languages_spoken)) ? ($langs[$languages_spoken[1]]) : '';
      $languages_spoken_array['language_3'] = (array_key_exists(2, $languages_spoken)) ? ($langs[$languages_spoken[2]]) : '';
      $languages_spoken_array['language_4'] = (array_key_exists(3, $languages_spoken)) ? ($langs[$languages_spoken[3]]) : '';

      $website = ($line[34]) ? $line[35] : '';
      
      $registry->loadArray($languages_spoken_array);

      $languages = (string) $registry;

      $query->clear();

      $query->insert('#__property_versions');

      $query->columns(array(
          'property_id', 'title', 'country', 'area', 'region', 'department', 'city', 'location_details',
          'local_amenities', 'getting_there', 'airport', 'location_type', 'latitude', 'longitude',
          'distance_to_coast', 'video_url', 'booking_form', 'deposit_currency', 'security_currency',
          'deposit', 'security_deposit', 'payment_deadline', 'evening_meal', 'additional_booking_info',
          'terms_and_conditions', 'use_invoice_details', 'first_name', 'surname', 'address', 'phone_1',
          'phone_2', 'phone_3', 'fax', 'email_1', 'email_2', 'website', 'review', 'created_on', 'created_by',
          'modified_on', 'modified_by', 'published_on', 'languages_spoken','lwl','frtranslation'
      ));

      $insert_string = '';
      // May need revising
      $insert_string .= $line[0];

      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[1]), $cp1252_map));
      $insert_string .= ',' . 2; // France
      $insert_string .= ',' . $line[2];
      $insert_string .= ',' . $line[3];
      $insert_string .= ',' . $line[4];
      $insert_string .= ',' . $db->quote($items[0]);
      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[6]), $cp1252_map)); // Location details
      $insert_string .= ',' . $db->quote($line[7]); // Local amenities
      $insert_string .= ',' . $db->quote($line[8]); // Getting there
      $insert_string .= ',' . $db->quote($airport[0]); // Airport - sql this as per city
      $insert_string .= ',' . $db->quote($line[10]); // Location type
      $insert_string .= ',' . $db->quote($line[11]); // Latitude
      $insert_string .= ',' . $db->quote($line[12]); // Longitude
      $insert_string .= ',' . $db->quote($line[13]); // Distance to coast
      $insert_string .= ',' . $db->quote($line[14]); // video url
      $insert_string .= ',' . $db->quote($line[15]); // Booking form
      $insert_string .= ',' . $db->quote($line[16]); // Deposit currency
      $insert_string .= ',' . $db->quote($line[17]); // security deposit currency
      $insert_string .= ',' . $db->quote($line[18]); // deposit
      $insert_string .= ',' . $db->quote($line[19]); // security deposit
      $insert_string .= ',' . $db->quote($line[20]); // payment deadline
      $insert_string .= ',' . $db->quote($line[21]); // evening meal
      $insert_string .= ',' . $db->quote($line[22]); // additional booking info
      $insert_string .= ',' . $db->quote($line[23]); // tandc
      $insert_string .= ',' . $db->quote($line[24]); // use invoice details
      $insert_string .= ',' . $db->quote($line[25]);
      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[26]), $cp1252_map));
      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[27]), $cp1252_map));
      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[28]), $cp1252_map));
      $insert_string .= ',' . $db->quote(strtr(utf8_encode($line[29]), $cp1252_map));
      $insert_string .= ',' . $db->quote($line[30]);
      $insert_string .= ',' . $db->quote($line[31]);
      $insert_string .= ',' . $db->quote($line[32]);
      $insert_string .= ',' . $db->quote($line[33]);
      $insert_string .= ',' . $db->quote($website); // website
      $insert_string .= ',0'; // Review     
      $insert_string .= ',' . $db->quote($line[36]); // date created
      $insert_string .= ',' . $db->quote($line[37]); // owner
      $insert_string .= ',' . $db->quote($line[38]); // modified
      $insert_string .= ',1'; // Modified by
      $insert_string .= ',' . $db->quote($line[39]); // published on
      $insert_string .= ',' . $db->quote($languages); // json encoded languages spoken
      $insert_string .= ',' . $line[42];
      $insert_string .= ',' . $line[43];

      $query->values($insert_string);
      $db->setQuery($query,0,0);

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

