<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_enquiries'))
{
  return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::import('frenchconnections.library');

// Register the Helloworld helper file
JLoader::register('EnquiriesHelper', dirname(__FILE__) . '/helpers/enquiries.php');

JLoader::register('RentalHelper', JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller 
$controller = JControllerLegacy::getInstance('Enquiries');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
