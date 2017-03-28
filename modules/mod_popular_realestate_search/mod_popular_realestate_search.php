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

$app  = JFactory::getApplication();

// $popular = &modPopularRealestateSearchHelper::getPopularSearches();


$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'modPopularRealestateSearchHelper';
$cacheparams->method = 'getPopularSearches';

// Attempt to get popular searches from cache
$popular = JModuleHelper::moduleCache($module, $params, $cacheparams);

// Get popular regional searches from cache as well if possible
$cacheparams->methodparams = 3;

$regions = JModuleHelper::moduleCache($module, $params, $cacheparams);

require JModuleHelper::getLayoutPath('mod_popular_realestate_search', $params->get('layout', 'default'));

$document = JFactory::getDocument();

