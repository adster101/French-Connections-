<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.admin', 'com_reviews')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
  
// Register the Helloworld helper file
JLoader::register('SpecialOffersHelper', dirname(__FILE__) . '/helpers/specialoffers.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller 
$controller = JControllerLegacy::getInstance('SpecialOffers');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();