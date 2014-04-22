<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_status
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$config	= JFactory::getConfig();
$user   = JFactory::getUser();
$db     = JFactory::getDbo();
$lang   = JFactory::getLanguage();
$input  = JFactory::getApplication()->input;

// Get the number of unread messages in your inbox.
$query	= $db->getQuery(true)
	->select('message_id, subject, date_time')
	->from('#__messages')
	->where('state = 0 AND user_id_to = ' . (int) $user->get('id'));

$db->setQuery($query);
$msgs = $db->loadObjectList();

require JModuleHelper::getLayoutPath('mod_owner_messages', $params->get('layout', 'default'));
