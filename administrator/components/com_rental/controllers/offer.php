<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class RentalControllerOffer extends JControllerForm
{
	protected function allowEdit($data = array()) { 
		// This is a point where we need to check that the user can edit this data. 
		// E.g. check that this user actually 'owns' this property and can hence edit availability
		return true;  //always allow to edit record 
	} 	
  
  protected function postSaveHook(JModel &$model, $validData = array()) {
    // This post save hook sorts out the case where a special offer 
    $task = $this->getTask();
    $id = JRequest::getVar('id','','GET','int');
  	$lang  = JFactory::getLanguage();
		$app   = JFactory::getApplication();

    $this->setMessage(
			JText::_('COM_RENTAL_HELLOWORLD_OFFER_DETAILS_UPDATED_SUCCESS')
    );
    
    if ($task == 'save') {
      

      
      // redirect back to the list of special offers for this property...
      $this->setRedirect(
        JRoute::_(
          'index.php?option=' . $this->option . '&view=offers&id=' . $id, false
        )
      );     
 
    }
   
  }
}
