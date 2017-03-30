<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.create', 'com_fcadmin')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$params = JComponentHelper::getParams('com_media');

define('COM_MEDIA_BASE',    JPATH_ROOT . '/' . $params->get($path, 'images'));

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Register the Helloworld helper file
JLoader::register('FcadminHelper', dirname(__FILE__) . '/helpers/fcadmin.php');

// Get an instance of the controller 
$controller = JControllerLegacy::getInstance('Fcadmin');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();