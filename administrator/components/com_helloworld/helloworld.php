<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_helloworld')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
 
// Register the Helloworld helper file
JLoader::register('HelloWorldHelper', dirname(__FILE__) . '/helpers/helloworld.php');

// Register the JHtmlProperty class
JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Define some global paths
define('COM_IMAGE_BASE',	JPATH_ROOT.'/images/property');
define('COM_IMAGE_BASEURL', JURI::root().'images/property');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('HelloWorld');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
