<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_submenu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_rental');

// Bootstrap the fc lib
JLoader::import('frenchconnections.library');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

// Get the listings for this owner
$items = ModListingHelper::getList();

$re_items = ModListingHelper::getRealEstate();

// Process the property list into a more useful object
// TO DO - This should probably be reusing the rental and realestate listings model...as per the livechat
// module
$listings = ModListingHelper::getPropertyList($items);

$re_listings = ModListingHelper::getRealestatePropertyList($re_items);

require JModuleHelper::getLayoutPath('mod_listings', $params->get('layout', 'default'));