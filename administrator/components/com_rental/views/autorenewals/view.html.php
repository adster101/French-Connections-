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
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js", true, false);
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/vat.js", 'text/javascript', true, false);

    JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    JToolBarHelper::title(($this->id) ? JText::sprintf('COM_RENTAL_HELLOWORLD_MANAGE_AUTO_RENEWAL', $this->id) : JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW'));

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    // We can save the new record
    JToolBarHelper::save('autorenewals.save', 'JTOOLBAR_SAVE');
    JToolBarHelper::cancel('autorenewals.cancel', 'JTOOLBAR_CANCEL');
    //JToolBarHelper::help('COM_RENTAL_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
  }

}
