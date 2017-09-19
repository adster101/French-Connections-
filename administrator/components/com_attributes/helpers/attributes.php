<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class AttributesHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		JHtmlSidebar::addEntry(JText::_('Manage Attributes'), 'index.php?option=com_attributes', $submenu == 'attributes');	
		JHtmlSidebar::addEntry(JText::_('Manage Attribute Types'), 'index.php?option=com_attributes&view=attributetypes', $submenu == 'attributetypes');
  }
	
   
  
  
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($messageId)) {
			$assetName = 'com_rental';
		}
		else {
			$assetName = 'com_rental.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state', 'hellworld.edit.reorder'
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

		return $session->get('com_rental.property.'.$propertyId.'.lang', $lang->getTag());
	}
}
