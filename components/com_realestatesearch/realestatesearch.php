<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

// Bootstrap the FC library
JLoader::import('frenchconnections.library');

// Register the JHtmlProperty class
// JLoader::register('RealestateSearchHelperRoute', JPATH_SITE . '/components/com_fcsearch/helpers/route.php');

$controller = JControllerLegacy::getInstance('realestatesearch');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
