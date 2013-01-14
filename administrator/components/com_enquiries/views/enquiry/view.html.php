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


 
    $this->addToolBar();
  	
    $this->setDocument();

    // Display the template
    parent::display($tpl);

  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $isNew = $this->item->id == 0;

    $document = JFactory::getDocument();
    
    $document->title(JText::sprintf('COM_ENQUIRIES_OFFER_EDIT', $this->item->forename, $this->item->surname), 'enquiry');
    
 		$document->addScript(JURI::root() . "/administrator/components/com_enquiries/js/submitbutton.js",false,true);
    
    JText::script('MUPPET!');
    
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    JToolBarHelper::save('specialoffer.save', 'JTOOLBAR_SAVE');
    JToolBarHelper::cancel('specialoffer.cancel', 'JTOOLBAR_CLOSE');
    JToolBarHelper::help('COM_SPECIALOFFERS_COMPONENT_HELP_VIEW', true);

    // Set the title which appears on the toolbar 
    
  }

}
