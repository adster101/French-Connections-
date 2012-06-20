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
		$lang 	   =& JFactory::getLanguage();
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
}
