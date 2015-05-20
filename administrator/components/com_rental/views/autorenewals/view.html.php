<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RentalViewAutorenewals extends JViewLegacy
{

  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   *
   * @return void
   */
  function display($tpl = null)
  {

    $app = JFactory::getApplication();
    $input = $app->input;

    $this->extension = $input->get('option', '', 'string');

    $this->id = $input->get('id', '', 'int');

    // Get the property listing details...
    $this->item = $this->get('Item');

    // Get the auto renewal options for this listing
    $this->form = $this->get('Form');

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
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
    $document = JFactory::getDocument();
    $document->addScript("/media/fc/js/general.js", 'text/javascript', true);
    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    JToolBarHelper::title(($this->id) ? JText::sprintf('COM_RENTAL_HELLOWORLD_MANAGE_AUTO_RENEWAL', $this->id) : JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW'));
    JToolBarHelper::custom('autorenewals.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);

    $bar = JToolbar::getInstance('actions');

    $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'autorenewals.save', false);

    // We can save the new record
    //JToolBarHelper::help('COM_RENTAL_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
  }

}
