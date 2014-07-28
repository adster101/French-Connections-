<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


// Include the module helper classes.
if (!class_exists('ModMenuHelper'))
{
	require realpath(__DIR__ . '/..') . '/mod_menu/helper.php';
  
}

if (!class_exists('JAdminCssMenu'))
{
	require realpath(__DIR__ . '/..') . '/mod_menu/menu.php';
}

if (!class_exists('FcAdminCssMenu'))
{
	require __DIR__ . '/fcmenu.php';
}

$lang    = JFactory::getLanguage();
$user    = JFactory::getUser();
$input   = JFactory::getApplication()->input;
$menu    = new FcAdminCSSMenu;
$enabled = $input->getBool('hidemainmenu') ? false : true;

// Render the module layout
require JModuleHelper::getLayoutPath('mod_fcmenu', $params->get('layout', 'default'));
