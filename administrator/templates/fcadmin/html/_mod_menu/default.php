<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$user = JFactory::getUser();

$groups = JAccess::getGroupsByUser($user->id, false);

$lang = JFactory::getLanguage();
$lang->load('frenchconnections', JPATH_SITE . '/libraries/frenchconnections');

$document = JFactory::getDocument();
$direction = $document->direction == 'rtl' ? 'pull-right' : '';

if (in_array(10, $groups)) {
  require JModuleHelper::getLayoutPath('mod_menu', 'default_owner');
} elseif (in_array(11, $groups)) {
  require JModuleHelper::getLayoutPath('mod_menu', 'default_office');
} else {
  require JModuleHelper::getLayoutPath('mod_menu', 'default_enabled');
}

$menu->renderMenu('menu', 'nav ' . $direction);