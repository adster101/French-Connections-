<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerAvailability extends JControllerForm
{
	protected function allowEdit($data = array()) { 
		// This is a point where we need to check that the user can edit this data. 
		// E.g. check that this user actually 'owns' this property and can hence edit availability
		return true;  //always allow to edit record 
	} 
	
	protected function getRedirectToItemAppend(	$recordId = null, $urlVar = 'id') { 
		$append = parent::getRedirectToItemAppend(JRequest::getInt('id'), $urlVar ); 
		return $append; 
	} 
}
