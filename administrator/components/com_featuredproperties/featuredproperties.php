<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.admin', 'com_vouchers')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('FeaturedPropertiesHelper', dirname(__FILE__) . '/helpers/featuredproperties.php');

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('featuredproperties');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
