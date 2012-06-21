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
	
	public static function getAvailabilityCalendar($month, $year) 
	{
		/* draw table */
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */
		$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		/* row for week one */
		$calendar.= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		endfor;

		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
			$calendar.= '<td class="calendar-day">';
				/* add in the day number */
				$calendar.= '<div class="day-number">'.$list_day.'</div>';

				/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
				$calendar.= str_repeat('<p>&nbsp;</p>',2);
				
			$calendar.= '</td>';
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			endfor;
		endif;

		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';
		
		/* all done, return result */
		return $calendar;

	}
	
	
}
