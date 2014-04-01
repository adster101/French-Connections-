<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class AttributesViewAttribute extends JViewLegacy {

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

    // Sort out the sidebar menu
    $view = strtolower(JRequest::getVar('view'));

    // Get the editing language for editing the various attributes. 
    // Could maybe be set per attribute but this works. Set to en-GB by default.
    $this->lang = JApplication::getUserState('com_attributes.edit.lang','en-GB');

    $this->addToolBar();
  		
    // Display the template
    parent::display($tpl);

    $this->setDocument();

  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('Manage property attributes'));
 		$document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js");
		JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');

  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    JToolBarHelper::apply('attribute.apply', 'JTOOLBAR_APPLY');
    JToolBarHelper::save('attribute.save', 'JTOOLBAR_SAVE');
    JToolBarHelper::custom('attribute.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
    JToolBarHelper::custom('attribute.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
    JToolBarHelper::cancel('attribute.cancel', 'JTOOLBAR_CLOSE');

    // Set the title which appears on the toolbar 
  }

}
