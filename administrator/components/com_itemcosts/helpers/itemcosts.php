<?php
/**
 * @version     1.0.0
 * @package     com_itemcosts
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Itemcosts helper.
 */
class ItemcostsHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_ITEMCOSTS_TITLE_ITEMCOSTS'),
			'index.php?option=com_itemcosts&view=itemcosts',
			$vName == 'itemcosts'
		);
    
		JHtmlSidebar::addEntry(
			JText::_('Item cost categories'),
			'index.php?option=com_categories&extension=com_itemcosts',
			$vName == 'categories'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_itemcosts';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
