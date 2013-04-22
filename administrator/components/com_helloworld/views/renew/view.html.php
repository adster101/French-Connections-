<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class HelloWorldViewRenew extends JViewLegacy {

  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   * 
   * @return void
   */
  function display($tpl = null) {

    
    
    // Set the document
    $this->setDocument();
    
    // Set the document
    $this->addToolBar();
    
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
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/locate.js",'text/javascript',true, false);

    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/bootstrap-button.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');

	}
  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
        
  
    JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CLOSE');
    
    JToolBarHelper::help('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);

  }
  
}
