<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';


$lang = JFactory::getLanguage();

JLoader::import('frenchconnections.library');

$lang->load('com_fcsearch', JPATH_SITE, null, false, true);

$app = JFactory::getApplication();


require JModuleHelper::getLayoutPath('mod_re_search', $params->get('layout', 'default'));

$document = JFactory::getDocument();



