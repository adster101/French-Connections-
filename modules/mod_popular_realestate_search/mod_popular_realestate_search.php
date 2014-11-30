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

$regions = &modPopularRealestateSearchHelper::getPopularSearches(3);
$popular = &modPopularRealestateSearchHelper::getPopularSearches();

require JModuleHelper::getLayoutPath('mod_popular_realestate_search', $params->get('layout', 'default'));

$document = JFactory::getDocument();

