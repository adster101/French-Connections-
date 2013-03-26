<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class HelloWorldHelper {

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getStateOptions() {

    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_HELLOWORLD_HELLOWORLD_ACTIVE'));
    $options[] = JHtml::_('select.option', '0', JText::_('COM_HELLOWORLD_HELLOWORLD_INACTIVE'));
    $options[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
    return $options;
  }

  /*
   * Get a list of filter options for the review state of a property
   * 
   * @return  array An array of JHtmlOption elements
   */

  public static function getReviewOptions() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '0', JText::_('COM_HELLOWORLD_HELLOWORLD_UPDATED'));
    $options[] = JHtml::_('select.option', '1', JText::_('COM_HELLOWORLD_HELLOWORLD_FOR_REVIEW'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   * 
   * @return  array An array of JHtmlOption elements
   */

  public static function getSnoozeOptions() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '0', JText::_('COM_HELLOWORLD_HELLOWORLD_HIDE_SNOOZED'));
    $options[] = JHtml::_('select.option', '1', JText::_('COM_HELLOWORLD_HELLOWORLD_SHOW_SNOOZED'));
    return $options;
  }

  /**
   * Configure the Linkbar.
   */
  public static function addSubmenu($view = '') {

    //Get the current user id 
    $user = JFactory::getUser();



    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_PROPERTY_MENU'), '#');
    // We only want to present the edit location screen if we are editing a parent property
    //if ($parent_id == 1 || empty($parent_id)) {
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_LOCATION'), 'index.php?option=com_helloworld&task=location.edit&id=' . $id, $submenu == 'location');
    //}
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_PROPERTY'), 'index.php?option=com_helloworld&task=helloworld.edit&id=' . $id, $submenu == 'helloworld');
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_FACILITIES'), 'index.php?option=com_helloworld&task=facilities.edit&id=' . $id, $submenu == 'facilities');
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY'), 'index.php?option=com_helloworld&task=availability.edit&id=' . $id, $submenu == 'availability');
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS'), 'index.php?option=com_helloworld&task=tariffs.edit&id=' . $id, $submenu == 'tariffs');
    //JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_IMAGES'), 'index.php?option=com_helloworld&task=images.edit&id=' . $id, $submenu == 'images');

    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_QUICK_MENU'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_SMS_NOTIFICATIONS'), 'index.php?option=com_admin&view=profile&layout=edit&id=' . $user->id . '#sms', ($view == 'profile'));
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_RENTAL_ACCOMMODATION'), 'index.php?option=com_helloworld', ($view == 'properties'));
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_REALESTATE_ACCOMMODATION'), 'index.php?option=com_realestate', ($view == 'realestate'));
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_ENQUIRIES'), 'index.php?option=com_enquiries', ($view == 'enquiries'));
  }

  /*
   * Method to get and check the status of the various key sections of a property listing
   * I.E. Property details, availability, tariffs and images.
   * 
   * params int id The property id of the property being editied
   *  
   * return void
   * 
   */

  public static function getPropertyProgress($id = '') {
    
  }

  /*
   * Helper function to update the state of the listing the user is currently editing.
   * N.B. This is only responsible for updating the parent listing details
   * 
   * @param JObject item The item (the property) being edited 
   * @param JObject units The list of units assigned to a particular property
   *  
   * return void
   * 
   */

  public static function setPropertyProgress($item = '', $units = array()) {

    // Collect the input from the request
    $input = JFactory::getApplication()->input;

    // Basic idea is that all edits of a listing must setup this data object. 
    // This will mean that all units and the listing data is available for any unit being editied
    // The id of the item being edited, could be unit or listing
    $id = $input->get('id', '', 'int');

    // The component - com_helloworld unless something has gone wrong!
    $option = $input->get('option', 'com_helloworld', 'string');

    // The user details
    $user = JFactory::getUser();

    // Create the 'progress' object
    $progress = new JObject;

    if (!empty($units)) {
      $units = $units->getProperties();
    }

    $listing_progress_array = array(
        'id' => $item->id,
        'title' => $item->title,
        'latitude' => $item->latitude,
        'longitude' => $item->longitude,
        'expiry' => $item->expiry_date,
        'units' => $units
    );

    // Check that this doesn't already exist in the session scope
    $progress->setProperties($listing_progress_array);
    JApplication::setUserState('listing', $progress);
    


    // May still need to use the below to check for images against the property listing
    //if (!JApplication::getUserState('com_hellworld.images.progress', false)) {
    // Import the model library 
    //$model = JModelLegacy::getInstance('Images', 'HelloWorldModel');
    // Use the getItem method to retrieve the image details. 
    //$item = $model->getItem($id);
    //if (array_key_exists('library', $item->images->library) && count($item->images->gallery->getProperties()) > 0) {
    //JApplication::setUserState('com_helloworld.images.progress', true);
    //} else if (count($item->images->library->getProperties()) > 0) {
    //JApplication::setUserState('com_helloworld.images.progress', true);
    //} else {
    //JApplication::setUserState('com_helloworld.images.progress', false);
    //}
    //}
    //}
  }

  public static function updateUnitProgress($data = '', $context = '') {

    // Get the listing details from the session 
    $listing = JApplication::getUserState('listing', false);

    $units = $listing->units;

    if (empty($units)) { // As units is empty this must be the first one for this listing.
      // Initialise a unit item for the listing object. This data has already been, checked, filtered and what not.
      $unit = new JObject;
      
      $unit->id = $data['id'];

 
    } else { // We already have a unit(s)
      if (array_key_exists($data['id'],$units) && $listing->listing_id == $data['parent_id']) {
        $listing->units = JArrayHelper::toObject($data, 'JObject');
      }

    }
    

    JApplication::setUserState('listing', $listing);
    
    
  }

  /**
   * Get the actions
   */
  public static function getActions($assetName = 'com_helloworld') {
    $user = JFactory::getUser();
    $result = new JObject;

    $actions = array(
        'core.admin',
        'core.manage',
        'core.create',
        'core.edit',
        'core.edit.own',
        'core.delete',
        'core.edit.state',
        'helloworld.edit.reorder',
        'helloworld.edit.publish',
        'helloworld.edit.property.owner',
        'helloworld.edit.property.parent',
        'helloworld.filter',
        'helloworld.display.owner',
        'helloworld.display.contactlog',
        'helloworld.sort.expiry',
        'helloworld.sort.prn',
        'helloworld.ownermenu.view',
        'helloworld.snooze',
        'helloworld.images.delete',
        'helloworld.images.edit',
        'helloworld.images.create'
    );


    foreach ($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }
    return $result;
  }

  /* Method to determine whether the currently logged in user is an 'owner' or an admin or something else...
   * 
   * 
   */

  public static function isOwner($editUserID = null) {

    // Get the user object and assign the userID to a var
    $user = JFactory::getUser($editUserID);


    // Get a list of the groups that the user is assigned to
    $groups = $user->getAuthorisedGroups();

    $group = array_pop($groups);

    if ($group === 10) {
      return true;
    } else {

      return false;
    }
  }

  /*
   * Get the default language 
   */

  public static function getDefaultLanguage() {
    $lang = & JFactory::getLanguage()->getTag();
    return $lang;
  }

  /*
   * Note, neither of these function beloware really needed as they already exist 
   * as utility functions in the framework.
   * 
   */

  public static function getLanguages() {
    $lang = & JFactory::getLanguage();
    $languages = $lang->getKnownLanguages(JPATH_SITE);

    $return = array();
    foreach ($languages as $tag => $properties)
      $return[] = JHTML::_('select.option', $tag, $properties['name']);
    return $return;
  }

  public static function getLang() {
    $session = & JFactory::getSession();
    $lang = & JFactory::getLanguage();
    $propertyId = JRequest::getInt('id');

    return $session->get('com_helloworld.property.' . $propertyId . '.lang', $lang->getTag());
  }

  /**
   * Generates HTML to display an availability calendar. 
   *
   * PHP Calendar (version 2.3), written by Keith Devens (adapted here)
   * http://keithdevens.com/software/php_calendar
   *
   * @param int $months The number of months to display the availability for 
   * @param array $availability The availability for this ID as an array.
   * @param type $day_name_length
   * @param type $first_day
   * @return string False on failure or error, true otherwise.
   */
  public static function getAvailabilityCalendar($months = 12, $availability = array(), $day_name_length = 2, $first_day = 0) {
    // Init calendar string
    $calendar = '';
    $calendar.='<table class="avCalendar">';
    // Get now
    $now = time();

    // Set the month and year as per now
    $month = date("m", $now);
    $year = date("y", $now);

    // The loop loops over some code which outputs a calendar. It does this $months times 
    for ($z = 0; $z <= $months; $z++) {
      if ($z % 4 == 0) {
        $calendar.="<tr>";
      }
      $calendar.="<td>";

      $first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
      #remember that mktime will automatically correct if invalid dates are entered 
      # for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998 
      # this provides a built in "rounding" feature to generate_calendar() 

      $day_names = array(); #generate all the day names according to the current locale 
      for ($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t+=86400) #January 4, 1970 was a Sunday 
        $day_names[$n] = ucfirst(gmstrftime('%A', $t)); #%A means full textual day name 

      list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));
      $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day 
      $title = htmlentities(ucfirst($month_name)) . '&nbsp;' . $year;  #note that some locales don't capitalize month and day names 

      $calendar .= '<p class="month-year">' . $title . '</p>' . "\n" . '<table class="avCalendarMonth">' . "\n";

      if ($day_name_length) { #if the day names should be shown ($day_name_length > 0) 
        #if day_name_length is >3, the full name of the day will be printed 
        foreach ($day_names as $d)
          $calendar .= '<th abbr="' . htmlentities($d) . '">' . htmlentities($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</th>';
        $calendar .= "</tr>\n<tr>";
      }

      if ($weekday > 0)
        $calendar .= '<td colspan="' . $weekday . '">&nbsp;</td>';#initial 'empty' days 
      for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {
        if ($weekday == 7) {
          $weekday = 0; #start a new week 
          $calendar .= "</tr>\n<tr>";
        }

        $today = date('Y-m-d', gmmktime(0, 0, 0, $month, $day, $year));

        if (array_key_exists($today, $availability)) {
          if ($availability[$today]) { // Availability is true, i.e. available
            $calendar .= '<td class="available">' . $day . '</td>';
          } else { // Availability is false i.e. unavailable
            $calendar .= '<td class="unavailable">' . $day . '</td>';
          }
        } else { // Availability not defined for this day so we default to unavailable
          $calendar .= '<td class="unavailable">' . $day . '</td>';
        }
      }
      if ($weekday != 7)
        $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';#remaining "empty" days 

      $calendar.="</table></td>";
      if ($z % 4 == 3) {
        $calendar.="</tr>";
      }
      $month++;
    }
    $calendar.="</table>";

    return $calendar;
  }

  /**
   *  Generates an array containing availability for each availability period stored for the property
   *
   * Returns an array of available days based on available periods.
   * 
   * @param array $availability An array of availability periods as stored against a property
   * @param Date $new_start_date The start date of a new availability period 
   * @param Date $new_end_date  The end date of a new availability period
   * @param boolean $new_availability_status The status for the availability period being updated
   * @return array An array of availability, by day. If new start and end dates are passed then these are included in the returned array
   * 
   */
  public static function getAvailabilityByDay($availability_by_day = array(), $start_date = '', $end_date = '', $availability = false) {
    // Array to hold availability per day for each day that availability has been set for.
    // This is needed as availability is stored by period, but displayed by day.
    $raw_availability = array();

    // Generate a DateInterval object which is re-used in the below loop
    $DateInterval = new DateInterval('P1D');

    // For each availability period passed in 	
    foreach ($availability_by_day as $availability_period) {

      // Convert the availability period start date to a PHP date object
      $availability_period_start_date = new DateTime($availability_period->start_date);

      // Convert the availability period end date to a date 
      $availability_period_end_date = new DateTime($availability_period->end_date);

      // Calculate the length of the availability period in days
      $availability_period_length = date_diff($availability_period_start_date, $availability_period_end_date);

      // Set the first day of the availability period to available/unavailable
      $raw_availability[date_format($availability_period_start_date, 'Y-m-d')] = $availability_period->availability;

      // Loop from the start date to the end date adding an available day to the availability array for each availalable day
      for ($i = 1; $i <= $availability_period_length->days; $i++) {

        // Add one day to the start date for each day of availability
        $date = $availability_period_start_date->add($DateInterval);

        // Add the day as an array key storing the availability status as the value
        $raw_availability[date_format($date, 'Y-m-d')] = $availability_period->availability;
      }
    }

    // If additional availability has been added then we need to add that to the array as well.
    if ($start_date && $end_date) {
      // Convert the availability period start date to a PHP date object
      $availability_period_start_date = new DateTime($start_date);

      // Convert the availability period end date to a date 
      $availability_period_end_date = new DateTime($end_date);

      // Calculate the length of the availability period in days
      $availability_period_length = date_diff($availability_period_start_date, $availability_period_end_date);

      // Loop from the start date to the end date adding an available day to the availability array for each availalable day
      for ($i = 0; $i <= $availability_period_length->days; $i++) {

        $raw_availability[date_format($availability_period_start_date, 'Y-m-d')] = $availability;

        // Add one day to the start date for each day of availability
        $date = $availability_period_start_date->add($DateInterval);
      }
    }

    return $raw_availability;
  }

  /**
   * Given an array of availability by day returns an array of availability periods, ready for insert into the db
   *  
   * @param array $availability_by_day An array of days containing the availability status
   * @return array An array of availability periods
   * 
   */
  public static function getAvailabilityByPeriod($availability_by_day = array()) {
    $current_status = '';
    $availability_by_period = array();
    $counter = 0;

    $last_date = key(array_slice($availability_by_day, -1, 1, TRUE));

    foreach ($availability_by_day as $day => $status) {
      if (($status !== $current_status) || ( date_diff(new DateTime($last_date), new DateTime($day))->days > 1 )) {
        $counter++;
        $availability_by_period[$counter]['start_date'] = $day;
        $availability_by_period[$counter]['end_date'] = $day;
        $availability_by_period[$counter]['status'] = $status;
      } else {
        $availability_by_period[$counter]['end_date'] = $day;
      }

      $current_status = $status;
      $last_date = $day;
    }
    return $availability_by_period;
  }

  /**
   * Displays a calendar control field
   *
   * @param   string  $value    The date value
   * @param   string  $name     The name of the text field
   * @param   string  $id       The id of the text field
   * @param   string  $format   The date format
   * @param   array   $attribs  Additional HTML attributes
   *
   * @return  string  HTML markup for a calendar field
   *
   * @since   11.1
   */
  public static function linkedcalendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null) {
    static $done;

    if ($done === null) {
      $done = array();
    }

    $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
    $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
    if (is_array($attribs)) {
      $attribs = JArrayHelper::toString($attribs);
    }

    if (!$readonly && !$disabled) {
      // Load the calendar behavior
      JHtml::_('behavior.tooltip');


      return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id
              . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
              . JHtml::_('image', 'system/calendar.png', JText::_('JLIB_HTML_CALENDAR'), array('class' => 'calendar', 'id' => $id . '_img'), true);
    } else {
      return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '')
              . '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, JFactory::getDbo()->getDateFormat()) : '') . '" ' . $attribs
              . ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
    }
  }

}
