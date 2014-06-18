<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewImage extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {
    // Get the property ID from the GET variable
    $this->property_id = JRequest::getVar('property_id', '', 'GET', 'int');

    // Get the image file ID of which we need to delete
    $this->file_id = JRequest::getVar('id', '', 'GET', 'int');

    // Get the caption details for the image
    $this->form = $this->get('Form');

    // Set the toolbar
    $this->addToolBar();

    // Display the template
    parent::display($tpl);

    // Set the document
    $this->setDocument();
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    // Determine the layout we are using. 
    // Should this be done with views? 
    $view = strtolower(JRequest::getVar('view'));

    $user = JFactory::getUser();
    $userId = $user->id;

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    JToolBarHelper::title($listing->title ? JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $listing->title, $listing->id) : JText::_('COM_RENTAL_MANAGER_HELLOWORLD_EDIT'));

 


    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::cancel('images.cancel', 'JTOOLBAR_CANCEL');

   
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();

    // Get the listing details from the session...
    $listing = JApplication::getUserState('listing', false);

    $document->setTitle($listing->title ? JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $listing->title, $listing->id) : JText::_('COM_RENTAL_MANAGER_HELLOWORLD_EDIT'));
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/submitbutton.js");

    $document->addScript(JURI::root() . "administrator/components/com_rental/js/vendor/jquery.ui.widget.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Templates/tmpl.min.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery.iframe-transport.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery.fileupload.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery.fileupload-fp.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery.fileupload-ui.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/main.js", 'text/javascript', true, false);
    
    
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/helloworld.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/jquery.fileupload-ui.css", 'text/css', "screen");

    JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

}
