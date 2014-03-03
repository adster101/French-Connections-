<?php

/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die;

require_once (dirname(__FILE__).'/helper.php');

require_once(JPATH_SITE.'/components/com_content/helpers/route.php');

// Register the heneral help helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

// Register the FCSearchHelperRoute class
JLoader::register('FCSearchHelperRoute', JPATH_SITE . '/components/com_fcsearch/helpers/route.php');

$helper = new modFeaturedPropertyHelper();

$items = $helper->getFeaturedProperties($params);

require JModuleHelper::getLayoutPath('mod_featuredproperty', $params->get('layout', 'default'));


