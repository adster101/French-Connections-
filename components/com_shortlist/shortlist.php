<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Register the Helloworld helper file
//JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

$controller = JControllerLegacy::getInstance('Shortlist');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();