<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class HelloWorldHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu, $published = 0) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		//JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_LOCATION'), 'index.php?option=com_helloworld&task=location.edit&id='.$id, $submenu == 'location');
		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_PROPERTY'), 'index.php?option=com_helloworld&task=helloworld.edit&id='.$id, $submenu == 'helloworld');	
		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY'), 'index.php?option=com_helloworld&task=availability.edit&id='.$id, $submenu == 'availability');		
		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS'), 'index.php?option=com_helloworld&task=tariffs.edit&id='.$id, $submenu == 'tariffs');		
		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_IMAGES'), 'index.php?option=com_helloworld&task=images.edit&id='.$id, $submenu == 'images');		
		if ($id != '' && $published) {
      JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_OFFERS'), 'index.php?option=com_helloworld&view=offers&id='.$id, $submenu == 'offers');		    
    }

    
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-location {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-availability {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-tariffs {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-images {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-offers {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');

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

  public static function setPropertyProgress($id = 0, $published = 0) 
  { 
    
    $task =  JRequest::getVar('task', '', 'GET');

    if(($task == '' || $task == 'edit') && $id !=0) {

      // Check that this doesn't already exist in the session scope
      if (!JApplication::getUserState('com_helloworld.availability.progress', false))
      {        

        // Get an instance of the availability table
        $table = JTable::getInstance('Availability', 'HelloWorldTable', array());

        // Load the availability for this property
        $availability = $table->load($id);

        // Check for errors.
        if (count($errors = $table->get('Errors'))) {
          JError::raiseError(500, implode('<br />', $errors));
          return false;
        }

        // Set the userstate accordingly
        if (count($availability)) {
          JApplication::setUserState('com_helloworld.availability.progress', true);
        } else {
          JApplication::setUserState('com_helloworld.availability.progress', false);
        }
      }

      // Check that this doesn't already exist in the session scope
      if (!JApplication::getUserState('com_helloworld.tariffs.progress', false))
      {    
        // Get an instance of the tariffs table
        $table = JTable::getInstance('Tariffs', 'HelloWorldTable', array());

        // Load the availability for this property
        $tariffs = $table->load($id);

        // Check for errors.
        if (count($errors = $table->get('Errors'))) {
          JError::raiseError(500, implode('<br />', $errors));
          return false;
        }

        // Set the context and userstate accordingly
        if (count($tariffs)) {
          JApplication::setUserState('com_helloworld.tariffs.progress', true);
        } else {
          JApplication::setUserState('com_helloworld.tariffs.progress', false);        
        }
      }

      // Set the property's published status in the session
      if ($published) {
        JApplication::setUserState('com_helloworld.published.progress', true);
      } else {
        JApplication::setUserState('com_helloworld.published.progress', false);
      }
      
      
      // Check that this doesn't already exist in the session scope
      if(!JApplication::getUserState('com_hellworld.images.progress', false))
      {

        // Import the model library 
        $model = JModel::getInstance('Images', 'HelloWorldModel');

        // Use the getItem method to retrieve the image details. 
        $item = $model->getItem($id);


        if (array_key_exists('gallery' , $item->images->gallery) && count($item->images->gallery->getProperties()) > 0) 
        {

          JApplication::setUserState('com_helloworld.images.progress', true);

        } 

        else if ( count($item->images->gallery->getProperties()) > 0 )
        {

          JApplication::setUserState('com_helloworld.images.progress', true);

        } 

        else

        {
          JApplication::setUserState('com_helloworld.images.progress', false);

        }
      }
    }
  }
  
  
  
  
  
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($messageId)) {
			$assetName = 'com_helloworld';
		}
		else {
			$assetName = 'com_helloworld.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state', 'hellworld.edit.reorder', 'helloworld.edit.publish'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result;
	}
	
  /* Method to determine whether the currently logged in user is an 'owner' or an admin or something else...
   * 
   * 
   */
  
  public static function isOwner($editUserID = null) {
    
    // Get the user object and assign the userID to a var
    $user		= JFactory::getUser($editUserID);
    
    
    // Get a list of the groups that the user is assigned to
    $groups = $user->getAuthorisedGroups();
    
    $group = array_pop($groups);
    
    if ($group === 10)
		{
			return true;
      
    } else {
      
      return false;
      
    }   
  }  
	/*
	 * Get the default language 
	 */
	
	public static function getDefaultLanguage()
	{
		$lang = & JFactory::getLanguage()->getTag();
		return $lang;
	}

	public static function getLanguages()
	{
		$lang 	   = & JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		
		$return = array();
		foreach ($languages as $tag => $properties)
			$return[] = JHTML::_('select.option', $tag, $properties['name']);
		return $return;
	}
	
	public static function getLang()
	{
		$session =& JFactory::getSession();
		$lang 	 =& JFactory::getLanguage();
		$propertyId = JRequest::getInt('id');

		return $session->get('com_helloworld.property.'.$propertyId.'.lang', $lang->getTag());
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

	public static function getAvailabilityCalendar($months=12, $availability= array(), $day_name_length = 2, $first_day = 0)
	{ 
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
	public static function getAvailabilityByDay ( $availability_by_day = array(), $start_date = '', $end_date= '', $availability = false ) 
	{
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
  public static function getAvailabilityByPeriod ( $availability_by_day = array() ) 
  {
    $current_status = '';
    $availability_by_period = array(); 
    $counter = 0;
    
    $last_date = key(array_slice($availability_by_day, -1,1, TRUE));
    
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
	public static function linkedcalendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
	{
		static $done;

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		if (!$readonly && !$disabled)
		{
			// Load the calendar behavior
			JHtml::_('behavior.tooltip');

		
			return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id
				. '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
				. JHtml::_('image', 'system/calendar.png', JText::_('JLIB_HTML_CALENDAR'), array('class' => 'calendar', 'id' => $id . '_img'), true);
		}
		else
		{
			return '<input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '')
				. '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, JFactory::getDbo()->getDateFormat()) : '') . '" ' . $attribs
				. ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
		}
	}
}
