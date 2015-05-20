<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Bootstrao the fc library for the usk us gubbins
JLoader::import('frenchconnections.library');

$controller = JControllerLegacy::getInstance('fccontact');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();