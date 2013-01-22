<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewNew extends JViewLegacy {

  protected $state;

  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   * 
   * @return void
   */
  function display($tpl = null) {

    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get data from the model
    $items = JApplication::getUserState("com_helloworlds_property_count_$userID");

    // Check the users permission to change owner
    // If they can change owner this must be an admin user
    if ($user->authorise('helloworld.edit.property.owner', 'com_helloworld')) {
    
      // Set documnet title
      $this->document->setTitle(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_CHOOSE_OWNER'));

      JToolbarHelper::title(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_CHOOSE_OWNER'));
      
      $form = $this->get('NewAdminPropertyForm');
     
    } else {
      // Set documnet title
      $this->document->setTitle(JText::_('COM_HELLOWORLD_HELLOWORLD_ADD_NEW_PROPERTY'));

      JToolbarHelper::title(JText::_('COM_HELLOWORLD_HELLOWORLD_ADD_NEW_PROPERTY'));
      
      // User doesn't have permission to change ownership of a property
      // If there already properties assigned to this account  
      if ($items > 0) {

        // We show the user a list of properties which they may co-locate a property with
        // Rather than calling a method from the model use JForm static helper
        $form = $this->get('NewPropertyForm');
        
      } else {
        // No forms so just set $form for neatness
        $form = null;
      }
    }

    // Assign data to the view
    $this->items = $items;

    $this->form = $form;

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }
    
    // Set the document
    $this->setDocument();
    
    // Display the template
    parent::display($tpl);
  }
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js", false, true);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/bootstrap-button.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');

	}
}
