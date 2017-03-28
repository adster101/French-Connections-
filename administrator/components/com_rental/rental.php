<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_rental')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::import('frenchconnections.library');


// Register the Preview button
JLoader::register('JToolbarButtonPreview', JPATH_ROOT . '/administrator/components/com_rental/buttons/preview.php'); 

// Register the Helloworld helper file
JLoader::register('RentalHelper', dirname(__FILE__) . '/helpers/rental.php');

// Register the JHtmlProperty class
//JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

// Register the Special Offers helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Define some global paths
define('COM_IMAGE_BASE',	JPATH_ROOT.'/images/property');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Rental');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
