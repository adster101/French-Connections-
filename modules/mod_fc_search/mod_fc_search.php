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

// Register the JHtmlProperty class
JLoader::register('FCSearchHelperRoute', JPATH_SITE . '/components/com_fcsearch/helpers/route.php');

$lang->load('com_fcsearch', JPATH_SITE, null, false, true);

$app = JFactory::getApplication();

$regions = &modFcSearchHelper::getSearchRegions();
$popular = &modFcSearchHelper::getPopularSearches();

require JModuleHelper::getLayoutPath('mod_fc_search', $params->get('layout', 'default'));

$document = JFactory::getDocument();

$document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.8.23.custom.min.js', 'text/javascript');
$document->addScript(JURI::root() . 'media/fc/js/date-range.js', 'text/javascript', true);
$document->addScript(JURI::root() . 'media/fc/js/search.js', 'text/javascript', true);
$document->addScript(JURI::root() . 'media/fc/js/general.js', 'text/javascript', true);
$document->addStyleSheet(JURI::root() . 'media/fc/css/jquery-ui-1.8.23.custom.css');

