<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class InterestingPlacesHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		JHtmlSidebar::addEntry(JText::_('Places of interest'), 'index.php?option=com_interestingplaces', $submenu == 'interestingplaces');
  }
  

	/**
	 * Get the actions
	 */
	public static function getActions($classificationId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($classificationId)) {
			$assetName = 'com_interestingplaces';
		}
		else {
			$assetName = 'com_interestingplaces.interestingplace.'.(int) $classificationId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.edit.own', 'core.edit.state'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result; 
	}  
}
