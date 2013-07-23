<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewAvailability extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {

    // Get the property ID we are editing.
    $this->item->id = JRequest::getVar('unit_id');

    // Add the Listing model to this view, so we can get the progress stuff
    $this->setModel(JModelLegacy::getInstance('Listing', 'HelloWorldModel', array('ignore_request' => true)));

    // Get the unit item we are editing the availability for...
    $this->unit = $this->get('Item');

    //Populate the availability model state
    $this->state = $this->get('State');

    // Get the availability form, which is loaded in a modal
    $this->form = $this->get('Form');

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');
    $progress->setState('com_helloworld.listing.id', $this->unit->property_id);
    $this->progress = $progress->getItems();

    // Get the availability for this unit
    $this->availability = $this->get('Availability');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Get availability as an array of days
    $this->availability_array = HelloWorldHelper::getAvailabilityByDay($availability = $this->availability);

    // Build the calendar taking into account current availability...
    $this->calendar = HelloWorldHelper::getAvailabilityCalendar($months = 18, $availability = $this->availability_array);

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

    // Get component level permissions
    $canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(($this->unit->unit_title) ? JText::sprintf('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY', $this->unit->unit_title) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

    $bar = JToolBar::getInstance('toolbar');

    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CANCEL');

    JToolBarHelper::help('', '');

    // Display a helpful navigation for the owners
    if ($canDo->get('helloworld.ownermenu.view')) {

      $view = strtolower(JRequest::getVar('view'));

      $canDo = HelloWorldHelper::addSubmenu($view);

      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();
    }
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $isNew = $this->item->id == 0;
    $document = JFactory::getDocument();
    $document->setTitle($isNew ? JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING') : JText::_('COM_HELLOWORLD_HELLOWORLD_EDITING'));
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/availability.js", false, true);
    $document->addStyleSheet(JURI::root() . "/administrator/components/com_helloworld/css/availability.css", 'text/css', "screen");
    JText::script('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_START_DATE');
    JText::script('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_END_DATE');
  }

}
