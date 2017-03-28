<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('frenchconnections.library');

$controller = JControllerLegacy::getInstance('fcsearch');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
