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

JLoader::register('VouchersHelper', dirname(__FILE__) . '/helpers/vouchers.php');

JLoader::register('InvoicesHelper', JPATH_ADMINISTRATOR . '/components/com_invoices/helpers/invoices.php');

$lang = JFactory::getLanguage();
$lang->load('com_invoices', JPATH_ADMINISTRATOR, null, false, true);

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('vouchers');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
