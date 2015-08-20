<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_realestate')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::import('frenchconnections.library');

// Register the Realestate helper file
JLoader::register('RealEstateHelper', dirname(__FILE__) . '/helpers/realestate.php');

// Register the Special Offers helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Define some global paths
define('COM_IMAGE_BASE',	JPATH_ROOT.'/images/property');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('RealEstate');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
