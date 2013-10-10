<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class FeaturedPropertiesViewFeaturedProperty extends JViewLegacy {

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
    
    $canDo = FeaturedPropertiesHelper::getActions();


    FeaturedPropertiesHelper::addSubmenu($view);
    $this->sidebar = JHtmlSidebar::render();

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
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('Manage featured property slot'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    
    // Is this a new property?
    $isNew = $this->item->id == 0;

    JToolBarHelper::title($isNew ? JText::_('COM_FEATUREDPROPERTY_ADDING_NEW') : JText::sprintf('COM_FEATUREDPROPERTY_EDITING_SLOT', $this->item->property_id), '');

    JToolBarHelper::apply('featuredproperty.apply', 'JTOOLBAR_APPLY');
    JToolBarHelper::save('featuredproperty.save', 'JTOOLBAR_SAVE');
    JToolBarHelper::custom('featuredproperty.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
    JToolBarHelper::cancel('featuredproperty.cancel', 'JTOOLBAR_CLOSE');
  }

}
