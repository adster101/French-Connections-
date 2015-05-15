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

$doc->addScript('//ajaxgeo.cartrawler.com/abe4.0/ct_loader.js?' . time(), 'text/javascript', false, true);

require JModuleHelper::getLayoutPath('mod_car_trawler', $params->get('layout', 'default'));
