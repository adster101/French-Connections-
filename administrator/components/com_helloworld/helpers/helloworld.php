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
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		//JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_LOCATION'), 'index.php?option=com_helloworld&task=location.edit&id='.$id, $submenu == 'location');
		
		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_PROPERTY'), 'index.php?option=com_helloworld&task=helloworld.edit&id='.$id, $submenu == 'helloworld');	

		JSubMenuHelper::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY'), 'index.php?option=com_helloworld&task=availability.edit&id='.$id, $submenu == 'availability');		
		
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-location {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
		$document->addStyleDeclaration('.icon-48-availability {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');
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
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.edit.own'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result;
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

	public static function getProperty()
	{
		return $propertyId;
	}
	
	/*
	 * Generates HTML to display an availability calendar. 
	 *
	 * PHP Calendar (version 2.3), written by Keith Devens (adapted here)
	 * http://keithdevens.com/software/php_calendar
	 *
	 * @param   array $availability  	The availability for this ID as an array.
	 * @param 	int $months 					The number of months to display the availability for 		
	 * 
	 * @return  string  False on failure or error, true otherwise.
	 *
	 * @since   1
	 */

	public static function getAvailabilityCalendar($months=12, $availability= array(), $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0)
	{ 
		// Init calendar string
		$calendar='';	
		$calendar.='<table>';
		// Get now
		$now = time();

		// Set the month and year as per now
		$month = date("m", $now);
		$year = date("y", $now);

		// The loop loops over some code which outputs a calendar. It does this $months times 
		for ($z=0;$z<=$months;$z++) {
			if($z % 4 == 0) {
				$calendar.="<tr>";	
			}
			$calendar.="<td>";
	
			$first_of_month = gmmktime(0,0,0,$month,1,$year); 
		  #remember that mktime will automatically correct if invalid dates are entered 
		  # for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998 
		  # this provides a built in "rounding" feature to generate_calendar() 

		  $day_names = array(); #generate all the day names according to the current locale 
		  for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday 
		      $day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name 

		  list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month)); 
		  $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day 
		  $title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names 
			 
		  $calendar .= '<p>'.$title.'</p>'."\n".'<table class="calendar">'."\n"; 

		  if($day_name_length) { #if the day names should be shown ($day_name_length > 0) 
		      #if day_name_length is >3, the full name of the day will be printed 
		      foreach($day_names as $d) 
		          $calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>'; 
		      $calendar .= "</tr>\n<tr>"; 
		  }

		  if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days 
		  for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++) { 
		      if($weekday == 7){ 
		          $weekday   = 0; #start a new week 
		          $calendar .= "</tr>\n<tr>"; 
		      } 
		      if(isset($days[$day]) and is_array($days[$day])){ 
		          @list($link, $classes, $content) = $days[$day]; 
		          if(is_null($content))  $content  = $day; 
		          $calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>'). 
		              ($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>'; 
		      } 
		      else $calendar .= "<td>$day". date('Y-m-d',gmmktime(0,0,0,$month,$day,$year))."</td>"; 
		  } 
		  if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days 
			
			$calendar.="</table></td>";
			if($z % 4 == 3) {
				$calendar.="</tr>";			
			}
			$month++;

		}
		$calendar.="</table>";

		return $calendar; 
	} 

	/*
	 * Generates an array containing availability for each availability period stored for the property
	 *
	 * @param   array $availability  	The availability for this ID as an array.
	 *
	 * Returns an array of available days based on available periods
	 *
	 */
	
	public static function getAvailabilityArray ( $availability = array() ) 
	{
		$raw_availability = array();
		// Loop over the availability	
		foreach ($availability as $availability_period) {
			if ($availability_period->availability) {				
				// Add this availability to the $raw_availability array
	
				// Convert the start date to a date 
				$start_date = new DateTime($availability_period->start_date);
	
				// Convert the end date to a date 
				$end_date = new DateTime($availability_period->end_date);
				
				$availability_period_length =  date_diff($start_date, $end_date);

				$raw_availability[date_format($start_date, 'Y-m-d')] = 'available';
				// Loop from the start date to the end date adding an available day to the availability array for each availalable day
				for ($i=1;$i<=$availability_period_length->days;$i++) {
					// Add one day to the start date for each day of availability
					$date = $start_date->add(new DateInterval('P1D'));
					// And stash it in the array for safe keeping
					$raw_availability[date_format($date, 'Y-m-d')] = 'available';
				}
			}		
		}	
		return $raw_availability;	
	}
}
