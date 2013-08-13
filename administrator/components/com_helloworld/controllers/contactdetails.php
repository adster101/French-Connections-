<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// TO DO - extend this controller and other that extend from controller form
// from another generic class which contain canEdit instead of defining in each controller
// Or simply import a utility class with this and other useful methods in
// from the libraries folder

/**
 * HelloWorld Controller
 */
class HelloWorldControllerContactDetails extends JControllerForm {
  
  public function postSaveHook(\JModelLegacy $model, $validData = array()) {
    
    $task = $this->getTask();
    
    if ($task == 'apply') {
      $this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=edit&property_id=' . $validData['property_id'], false
					)
				);
    } 
    
  
    
  }
  
  
}

