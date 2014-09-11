<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_realestate')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Register the Preview button
JLoader::register('JToolbarButtonPreview', JPATH_ROOT . '/administrator/components/com_realestate/buttons/preview.php'); 

// Register the Helloworld helper file
JLoader::register('RealestateHelper', dirname(__FILE__) . '/helpers/realestate.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Define some global paths
define('COM_IMAGE_BASE',	JPATH_ROOT.'/images/property');
define('COM_IMAGE_BASEURL', JURI::root().'images/property');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('RealEstate');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
