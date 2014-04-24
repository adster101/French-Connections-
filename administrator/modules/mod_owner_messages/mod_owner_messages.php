<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_status
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$config = JFactory::getConfig();
$user = JFactory::getUser();
$db = JFactory::getDbo();
$lang = JFactory::getLanguage();
$input = JFactory::getApplication()->input;

// Get the number of unread messages in your inbox.
$query = $db->getQuery(true)
        ->select('message_id, subject, date_time, state')
        ->from('#__messages')
        ->where('state in (0,1) AND user_id_to = ' . (int) $user->get('id'))
        ->order('date_time desc');


$db->setQuery($query, 0,5);
$msgs = $db->loadObjectList();

require JModuleHelper::getLayoutPath('mod_owner_messages', $params->get('layout', 'default'));
