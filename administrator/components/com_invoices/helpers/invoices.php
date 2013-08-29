<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Invoices helper.
 */
class InvoicesHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
    $user = JFactory::getUser();
  
		JHtmlSidebar::addEntry(
			JText::_('COM_INVOICES_TITLE_ACCOUNT_DETAILS'),
			'index.php?option=com_invoices&task=account.edit&user_id = ' . (int) $user->id ,
			$vName == 'account'
		);
    
    JHtmlSidebar::addEntry(
			JText::_('COM_INVOICES_TITLE_INVOICES'),
			'index.php?option=com_invoices&view=invoices',
			$vName == 'invoices'
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

		$assetName = 'com_invoices';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
