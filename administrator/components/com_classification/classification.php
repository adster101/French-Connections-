<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_classification')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
  
// Register the Helloworld helper file
JLoader::register('ClassificationHelper', dirname(__FILE__) . '/helpers/classification.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Classification
$controller = JControllerLegacy::getInstance('Classification');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();