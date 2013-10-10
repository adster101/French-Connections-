<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class EnquiriesHelper
{  
	/**
	 * Get the actions
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		$assetName = 'com_enquiries';
		
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

    return $result;
	} 
  
	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	public static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('COM_MESSAGES_OPTION_READ'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('COM_MESSAGES_OPTION_UNREAD'));
		$options[]	= JHtml::_('select.option',	'-2',	JText::_('JTRASHED'));
		return $options;
	}
}
