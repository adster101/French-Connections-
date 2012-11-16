<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class ClassificationHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		JHtmlSidebar::addEntry(JText::_('Manage Locations'), 'index.php?option=com_classification', $submenu == 'classifications');
  }
  

	/**
	 * Get the actions
	 */
	public static function getActions($classificationId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($classificationId)) {
			$assetName = 'com_classification';
		}
		else {
			$assetName = 'com_classification.classification.'.(int) $classificationId;
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
