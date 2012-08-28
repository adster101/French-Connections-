<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerHelloWorld extends JControllerForm
{
	protected function allowEdit($data = array()) { 
		// This is a point where we need to check that the user can edit this data. 
		// E.g. check that this user actually 'owns' this property and can hence edit availability
		return true;  //always allow to edit record 
	} 	
  
  
  public function woot() {
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
    
    $data = JRequest::getVar( 'jform', '', 'POST', 'array' );   
    if (array_key_exists('parent_id', $data)) {
      $parent_id = $data['parent_id'];     
    }
    $app = JFactory::getApplication();    
    $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_TO_BE_ADDED'), 'warning');

    $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&parent_id=' . $parent_id, false));

  }
 
}
