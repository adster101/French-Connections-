<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_interestingplaces')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
  
// Register the Helloworld helper file
JLoader::register('InterestingPlacesHelper', dirname(__FILE__) . '/helpers/interestingplaces.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Classification
$controller = JControllerLegacy::getInstance('InterestingPlaces');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();