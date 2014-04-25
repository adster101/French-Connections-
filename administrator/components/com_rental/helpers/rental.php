<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class RentalHelper {

  /**
   * Method to return the number of days until the property is due to expire
   *
   * @param type $expiry_date
   */
  public static function getDaysToExpiry($expiry_date = '') {

    $expiry_date = (!empty($expiry_date)) ? new DateTime($expiry_date) : '';

    if ($expiry_date) {
      $now = new DateTime(date('Y-m-d'));
      $days_to_renewal = $now->diff($expiry_date)->format('%R%a');
      //$days_to_renewal_pretty = $now->diff($expiry_date)->format('%a');
    }

    $days_to_renewal = (!empty($days_to_renewal)) ? $days_to_renewal : '';

    return $days_to_renewal;
  }

  /*
   * Determines a list of notices to display for a property notifying the user of which units and which sections need attention
   */

  public static function getProgressNotices($progress = array()) {

    $notices = array();
    // The sections we want to check for. Tariffs needs expanding for the more detailed tariff data (changeover day etc)
    $sections = array('images' => array(), 'availability' => array(), 'tariffs' => array(), 'contact_details' => array());
        
    if (empty($progress)) {
      return false;
    }

    if (empty($progress[0]->title)) {
      
    }




    if (empty($progress[0]->unit_title)) {
      
    }

    foreach ($progress as $unit) {
      if (empty($unit->unit_title)) {
        $notices['Accommodation']['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
      }

      $unit->contact_details = true;
    }

    if (!$progress[0]->use_invoice_details &&
            empty($progress[0]->first_name) &&
            empty($progress[0]->surname) &&
            empty($progress[0]->email_1) &&
            empty($progress[0]->phone_1)
    ) {
      $progress[0]->contact_details = false;
    }


    foreach ($sections as $section => $value) {

      foreach ($progress as $key => $unit) {
        if ($unit->$section == 0) { // If the unit doesn't have this section completed
          if (!array_key_exists($section, $notices)) {
            $notices[$section]['units'] = '';
          }

          // Add the unit that is failing to the list
          $notices[$section]['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
        }
      }
    }

    return $notices;
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getStateOptions() {

    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_ACTIVE'));
    $options[] = JHtml::_('select.option', '0', JText::_('COM_RENTAL_HELLOWORLD_INACTIVE'));
    $options[] = JHtml::_('select.option', '-1', JText::_('JTRASHED'));
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
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_UPDATED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_FOR_REVIEW'));
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
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_HIDE_SNOOZED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_SHOW_SNOOZED'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getDateFilterOptions() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '', JText::_('JSELECT'));
    $options[] = JHtml::_('select.option', 'expiry_date', 'Expiry date');
    $options[] = JHtml::_('select.option', 'created_on', 'Date created');
    return $options;
  }

  /**
   * This submenu is used between the rental property manager, special offers, review, enquiries and stats components.
   */
  public static function addSubmenu($view = '') {

    //Get the current user id
    $user = JFactory::getUser();

    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_SUBMENU_OWNERS_AREA_HOME'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_SUBMENU_OWNERS_HOME'), 'index.php');
    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_PROPERTY_SUBMENU_MENU'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_MENU'), 'index.php?option=com_rental', ($view == 'listings'));
    JHtmlSidebar::addEntry(JText::_('COM_SPECIALOFFERS_MENU'), 'index.php?option=com_specialoffers', ($view == 'specialoffers'));
    JHtmlSidebar::addEntry(JText::_('COM_ENQUIRIES_MENU'), 'index.php?option=com_enquiries', ($view == 'enquiries'));
    JHtmlSidebar::addEntry(JText::_('COM_REVIEWS_MENU'), 'index.php?option=com_reviews', ($view == 'reviews'));
    JHtmlSidebar::addEntry(JText::_('COM_STATS_MENU'), 'index.php?option=com_stats', ($view == 'stats'));
  }

  /**
   * Get the actions
   */
  public static function getActions($assetName = 'com_rental') {
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
        'helloworld.view.notes',
        'helloworld.notes.add',
        'helloworld.sort.expiry',
        'helloworld.sort.prn',
        'helloworld.ownermenu.view',
        'helloworld.snooze',
        'helloworld.images.delete',
        'helloworld.images.edit',
        'helloworld.images.reorder',
        'helloworld.images.create',
        'helloworld.property.submit',
        'helloworld.property.review',
        'helloworld.property.autorenew',
        'helloworld.property.preview',
        'helloworld.reports.renewal'
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

    return $session->get('com_rental.property.' . $propertyId . '.lang', $lang->getTag());
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
  public static function getAvailabilityCalendar($months = 12, $availability = array(), $day_name_length = 2, $first_day = 0, $link = true) {

    // Get the view
    $app = JFactory::getApplication();
    $view = $app->input->get('view', '', 'string');

    $calendar = '<div class="row-fluid">';

    $showlinks = ($view == 'availability') ? true : false;

    // Init calendar string
    // Get now
    $now = time();

    // Set the month and year as per now
    $month = date("m", $now);
    $year = date("y", $now);

    // The loop loops over some code which outputs a calendar. It does this $months times
    for ($z = 0; $z <= $months; $z++) {

      $calendar.='<div class="span3"><div class="calendar-container">';

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

      $calendar.= '<table class="table table-condensed avCalendar">' . "\n";
      $calendar.= '<thead><tr><th colspan="7"><p class="month-year">' . $title . '</p></th></tr><tr class="days">' . "\n";
      if ($day_name_length) { #if the day names should be shown ($day_name_length > 0)
        #if day_name_length is >3, the full name of the day will be printed
        foreach ($day_names as $d)
          $calendar .= '<th abbr="' . htmlentities($d) . '">' . htmlentities($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</th>';
      }

      $calendar.="</tr></thead>";

      if ($weekday > 0)
        $calendar .= '<td colspan="' . $weekday . '">&nbsp;</td>';#initial 'empty' days
      for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {
        if ($weekday == 7) {
          $weekday = 0; #start a new week
          $calendar .= "</tr>\n<tr>";
        }

        $today = date('Y-m-d', gmmktime(0, 0, 0, $month, $day, $year));
        $yesterday = date('Y-m-d', gmmktime(0, 0, 0, $month, $day - 1, $year));

        // Check whether availability status is set for the preceeding day
        $status = (array_key_exists($today, $availability)) ? $availability[$today] : false;
        $status_yesterday = (array_key_exists($yesterday, $availability)) ? $availability[$yesterday] : false;

        if ($status) { // Availability is true, i.e. available
          if ($status_yesterday != $status) {
            $calendar .= RentalHelper::generateDateCell($today, $day, array('unavailable-available'), $showlinks);
          } else {
            $calendar .= RentalHelper::generateDateCell($today, $day, array('available'), $showlinks);
          }
        } else { // Availability is false i.e. unavailable
          if ($status_yesterday != $status) {

            $calendar .= RentalHelper::generateDateCell($today, $day, array('available-unavailable'), $showlinks);
          } else {

            $calendar .= RentalHelper::generateDateCell($today, $day, array('unavailable'), $showlinks);
          }
        }
      }

      if ($weekday != 7) {
        $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>'; #remaining "empty" days
      }
      $calendar.="</table></div></div>";

      if (($z % 4 === 3)) {
        $calendar.='</div><div class="row-fluid">';
      }

      if ($z == $months) {
        $calendar.='</div>';
      }

      $month++;
    }



    return $calendar;
  }

  public function generateDateCell($today = '', $day = '', $classes = array(), $showlinks = false) {

    $return = '';

    $class = implode(' ', $classes);
    if (!empty($today)) {
      $return .= '<td data-date=' . $today . ' class="' . $class . '">';

      if ($showlinks) {
        $return .= '<a href="#">' . $day . '</a></td>';
      } else {
        $return .= '<span>' . $day . '</span>';
      }

      $return .= '</td>';
    }

    return $return;
  }

  /**
   * Generates an array containing availability for each availability period stored for the property
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
  public static function getAvailabilityByDay($availability_by_day = array(), $start_date = '', $end_date = '', $availability_status = false) {
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

        $raw_availability[date_format($availability_period_start_date, 'Y-m-d')] = $availability_status;

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
  public static function getAvailabilityByPeriod($availability_by_day = array(), $key = 'status') {
    $current_status = '';
    $availability_by_period = array();
    $counter = 0;

    $last_date = key(array_slice($availability_by_day, -1, 1, TRUE));

    foreach ($availability_by_day as $day => $status) {
      if (($status !== $current_status) || ( date_diff(new DateTime($last_date), new DateTime($day))->days > 1 )) {
        $counter++;
        $availability_by_period[$counter]['start_date'] = $day;
        $availability_by_period[$counter]['end_date'] = $day;
        $availability_by_period[$counter][$key] = $status;
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

  /*
   * Static helper method to return a list of units keyed by unit id.
   * This is used to pass into the accommodation tabs layout.
   *
   *
   * return   array $units
   *
   */

  public static function getUnitsbyId($data = array()) {

    if (empty($data)) {

      // This is a new property as no progress data has been returned (e.g. no listing or unit data)
      $units = array();

      $listing = new stdClass();
      $listing->id = '';
      $listing->review = '';
      $listing->unit_id = '';
      $listing->property_id = '';
      $listing->changeover_day = '';
      $listing->images = 0;
      $listing->tariffs = 0;
      $listing->availability = 0;
      $listing->firstname = '';
      $listing->surname = '';
      $listing->use_invoice_details = '';

      $units[] = $listing;

      return $units;
    }
    $units = array();

    foreach ($data as $key => $value) {

      if (empty($value->unit_id)) {
        $value->unit_id = 0;
      }

      if (!array_key_exists($value->unit_id, $units)) {
        $units[$value->unit_id] = $value;
      }
    }

    return $units;
  }

  public static function getEmptyUnit($listing_id = '') {

    // This is a new property as no progress data has been returned (e.g. no listing or unit data)

    $listing = new stdClass();
    $listing->id = $listing_id;
    $listing->review = '';
    $listing->unit_id = '';
    $listing->property_id = $listing_id;
    $listing->changeover_day = '';
    $listing->images = 0;
    $listing->tariffs = 0;
    $listing->availability = 0;

    return $listing;
  }

}
