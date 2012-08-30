<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
  
// Register the Helloworld helper file
//JLoader::register('HelloWorldHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'helloworld.php');
 
// import joomla controller library
jimport('joomla.application.component.controller');


// Get an instance of the controller prefixed by HelloWorld
$controller = JController::getInstance('Test');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();
