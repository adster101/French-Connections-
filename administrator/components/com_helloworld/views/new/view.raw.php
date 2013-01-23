<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class HelloWorldViewNew extends JViewLegacy
{
	protected $state;

	/**
	 * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   * 
	 * @return void
	 */
	function display($tpl = null) 
	{
        
    // Find the user details
    $user		= JFactory::getUser();
    $userID = $user->id;
   
		// Get data from the model
    $items = JApplication::getUserState("com_helloworlds_property_count_$userID");
            
    // If there already properties assigned to this account  
    if ($items > 0) {
      
      // We show the user a list of properties which they may co-locate a property with
      
      // Rather than calling a method from the model use JForm static helper
      // makes it a bit neater.
      $form = $this->get('NewPropertyForm');    
      // $form = $this->loadForm('com_helloworld.userproperties', 'userproperties', array('control' => 'jform', 'load_data' => $loadData));
      //$form = JForm::addFieldPath();
      //JForm::getInstance('com_helloworld.userproperties');
      // etc
      
    } else {
      // No forms so just set $form for neatness
      $form = null;
    }
    
		// Assign data to the view
		$this->items = $items;

    $this->form = $form;
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
	
		// Display the template
		parent::display($tpl);
     
	}

 
  
}