<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$uri = JUri::getInstance();

// Add car trawler scripts here...
// $doc->addScript('media/fc/js/date-range.js');

require JModuleHelper::getLayoutPath('mod_livechat', $params->get('layout', 'default'));
