<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class SpecialOffersViewSpecialOffer extends JViewLegacy
{

  /**
   * display method of Attribute view
   * @return void
   */
  public function display($tpl = null)
  {

    $canDo = SpecialOffersHelper::getActions();

    // get the Data
    $item = $this->get('Item');
    $form = $this->get('Form');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Assign the Data
    $this->item = $item;
    $this->form = $form;


    $this->addSubMenu();

    $this->addToolBar($canDo);

    $this->setDocument();

    // Display the template
    parent::display($tpl);
  }

  /**
   * Adds the submenu details for this view
   */
  protected function addSubMenu()
  {

    //RentalHelper::addSubmenu('specialoffers');
    //$this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $isNew = $this->item->id == 0;

    $document = JFactory::getDocument();

    JToolBarHelper::title($isNew ? JText::_('COM_SPECIALOFFER_OFFER_NEW') : JText::sprintf('COM_SPECIALOFFER_OFFER_EDIT', $this->item->id), 'helloworld');

    $document->addScript("/media/fc/js/general.js", false, true);
    $document->addScript("/media/fc/js/jquery-ui-1.8.23.custom.min.js", false, true);
    $document->addScript("/media/fc/js/date-range.js", false, true);
    $document->addStyleSheet("/media/fc/css/jquery-ui-1.8.23.custom.css");
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
  }

  /**
   * Setting the toolbar
   * 
   */
  protected function addToolBar($canDo)
  {
    // Only show the 'apply' button if you can edit state.
    if ($canDo->get('core.edit.state'))
    {
      JToolBarHelper::apply('specialoffer.apply');
    }

    if ($canDo->get('core.create'))
    {
      JToolBarHelper::save('specialoffer.save', 'JTOOLBAR_SAVE');
    }

    JToolBarHelper::cancel('specialoffer.cancel', 'JTOOLBAR_CLOSE');

    JToolBarHelper::help('COM_SPECIALOFFERS_COMPONENT_HELP_VIEW', true);
  }

}
