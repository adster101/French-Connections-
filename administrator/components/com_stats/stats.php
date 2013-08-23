<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_stats')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
 
// Register the component helper files
JLoader::register('HelloWorldHelper', JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/helloworld.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Stats');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
