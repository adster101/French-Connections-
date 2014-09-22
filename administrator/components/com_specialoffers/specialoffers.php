<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_specialoffers'))
{
  return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::import('frenchconnections.library');

// Register the Special Offers helper file
JLoader::register('SpecialOffersHelper', dirname(__FILE__) . '/helpers/specialoffers.php');

// Register the Special Offers helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . 'libraries/frenchconnections/html/helpers/general.php');

// Register the helloworld helper for the side navigation.
JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller 
$controller = JControllerLegacy::getInstance('SpecialOffers');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();