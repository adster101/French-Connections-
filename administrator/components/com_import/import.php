<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_import')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
  
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Register the Helloworld helper file
JLoader::register('ImportHelper', dirname(__FILE__) . '/helpers/import.php');

// Get an instance of the controller 
$controller = JControllerLegacy::getInstance('Import');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();