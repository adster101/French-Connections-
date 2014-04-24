<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewAvailability extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {

    // Get the property ID we are editing.
    $this->item->id = JRequest::getVar('unit_id');

    // Add the Listing model to this view, so we can get the progress stuff
    $this->setModel(JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true)));

    // Get the unit item we are editing the availability for...
    $this->unit = $this->get('Item');

    $this->unit->unit_title = (!empty($this->unit->unit_title)) ? $this->unit->unit_title : 'New unit';

    //Populate the availability model state
    $this->state = $this->get('State');

    // Get the availability form, which is loaded in a modal
    $this->form = $this->get('Form');

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');
    $progress->setState('com_rental.listing.id', $this->unit->property_id);
    $this->progress = $progress->getItems();

    // Get the availability for this unit
    $this->availability = $this->get('Availability');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Get availability as an array of days
    $this->availability_array = RentalHelper::getAvailabilityByDay($availability = $this->availability);

    // Build the calendar taking into account current availability...
    $this->calendar = RentalHelper::getAvailabilityCalendar($months = 18, $availability = $this->availability_array);

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
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_AVAILABILITY_EDIT', $this->unit->unit_title, $this->unit->property_id));

    $bar = JToolBar::getInstance('toolbar');

    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::custom('images.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    JToolBarHelper::cancel('availability.cancel', 'JTOOLBAR_CLOSE');
    JToolBarHelper::help('', '');
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $isNew = $this->item->id == 0;
    $document = JFactory::getDocument();
    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_AVAILABILITY_EDIT', $this->unit->unit_title, $this->unit->property_id));
    //$document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js");
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/availability.js", false, true);
    $document->addStyleSheet(JURI::root() . "/administrator/components/com_rental/css/availability.css", 'text/css', "screen");
    JText::script('COM_RENTAL_HELLOWORLD_AVAILABILITY_CHOOSE_START_DATE');
    JText::script('COM_RENTAL_HELLOWORLD_AVAILABILITY_CHOOSE_END_DATE');
  }

}
