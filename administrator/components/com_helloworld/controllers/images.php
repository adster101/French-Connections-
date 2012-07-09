<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerImages extends JControllerForm
{
	protected function allowEdit($data = array()) { 
		// This is a point where we need to check that the user can edit this data. 
		// E.g. check that this user actually 'owns' this property and can hence edit availability
		return true;  //always allow to edit record 
	}

  function upload () {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
    
    // Check that this user is authorised to edit this property
    $this->allowEdit();
    
    // Load the relevant table model so we can save the data back to the db
    $model = $this->getModel('images');
    
    // Get an instance of the helloworld table
    $table = $model->getTable();
    
    // Get the ID of the property being edited and set the table instance to that.
    $table->id = JRequest::getVar( 'id' );
    
    // Bind images to table object
    $array = array();
    
    
    $array['images'] = json_encode($_FILES);
    
  
    
    
    // Bind the translated fields to the JTAble instance	
    if (!$table->bind($array))
    {
      JError::raiseWarning(500, $table->getError());
      return false;
    }	
 
    // And update or create depending on whether any translations already exist
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}	
    
    //$table->save($id);
    
    
   print_r($_FILES);
    print_r($array);
    
    
    jexit(); // Exit this request now as results passed back to client via xhr transport.
  }
  
}
