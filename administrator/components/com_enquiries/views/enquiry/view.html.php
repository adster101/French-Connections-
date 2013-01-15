<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class EnquiriesViewEnquiry extends JViewLegacy {

  /**
   * display method of Attribute view
   * @return void
   */
  public function display($tpl = null) {
    // get the Data
    $item = $this->get('Item');
    $form = $this->get('Form');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Assign the Data
    $this->item = $item;
    $this->form = $form;

    $this->setDocument();

 
    $this->addToolBar();
  	

    // Display the template
    parent::display($tpl);

  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
   
    // Get the document object
    $document = JFactory::getDocument();
    
    // Set the site property
    $this->title = JText::sprintf('COM_ENQUIRIES_ENQUIRY_EDIT', $this->item->forename, $this->item->surname);
    
    // Set the document title
    $this->document->setTitle($this->title);   
    
 
    
 		$document->addScript(JURI::root() . "/administrator/components/com_enquiries/js/submitbutton.js",false,true);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    
    $canDo = EnquiriesHelper::getActions();
    
    if ($canDo->get('core.edit')) {
      JToolBarHelper::save('enquiry.reply', 'COM_ENQUIRIES_ENQUIRY_REPLY');
    }
    
    JToolBarHelper::cancel('enquiry.cancel', 'JTOOLBAR_CLOSE');
    
    JToolBarHelper::help('COM_SPECIALOFFERS_COMPONENT_HELP_VIEW', true);

    // Set the component toolbar title
    JToolbarHelper::title($this->title);    
  }

}
