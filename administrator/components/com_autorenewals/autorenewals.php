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
if (!JFactory::getUser()->authorise('core.manage', 'com_autorenewals')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('AutoRenewalsHelper', dirname(__FILE__) . '/helpers/autorenewals.php');

$controller	= JControllerLegacy::getInstance('autorenewals');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
