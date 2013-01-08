<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class ReviewsHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		JHtmlSidebar::addEntry(JText::_('Manage Reviews'), 'index.php?option=com_reviews', $submenu == 'reviews');	
  }
  
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_reviews';
		}
		else {
			$assetName = 'com_reviews.review.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

    return $result;
	} 
}
