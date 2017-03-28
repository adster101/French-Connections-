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

JLoader::import('frenchconnections.library');

$lang = JFactory::getLanguage();

$lang->load('com_fcsearch', JPATH_SITE, null, false, true);


//$regions = &modPopularSearchHelper::getPopularSearches(3);
//$popular = &modPopularSearchHelper::getPopularSearches();

$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'modPopularSearchHelper';
$cacheparams->method = 'getPopularSearches';

$params->set('cache_time',86400);

// Attempt to get popular searches from cache
$popular = JModuleHelper::moduleCache($module, $params, $cacheparams);

// Get popular regional searches from cache as well if possible
$cacheparams->methodparams = 3;
$regions = JModuleHelper::moduleCache($module, $params, $cacheparams);

require JModuleHelper::getLayoutPath('mod_popular_search', $params->get('layout', 'default'));


