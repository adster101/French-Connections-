<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewAvailability extends JViewLegacy
{

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null)
  {

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
    $model = $this->getModel('Listing');
    $model->setState('com_rental.listing.id', $this->unit->property_id);
    $this->progress = $model->getItems();

    $this->status = $model->getProgress($this->progress);

    // Get the property ID as the first item in the progress array
    $this->property_id = $this->progress[0]->id;

    // Get the availability for this unit
    $this->availability = $this->get('Availability');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Get availability as an array of days
    $this->availability_array = RentalHelper::getAvailabilityByDay($availability = $this->availability);

    // Build the calendar taking into account current availability...
    $this->calendar = RentalHelper::getOwnerAvailabilityCalendar($months = 18, $availability = $this->availability_array);

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
  protected function addToolBar()
  {

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_AVAILABILITY_EDIT', $this->unit->unit_title, $this->unit->property_id));
    JToolBarHelper::custom('availability.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);

    $bar = JToolbar::getInstance('actions');

    // We can save the new record
    $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'availability.apply', false);
    $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'availability.saveandnext', false);
    $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'availability.save', false);
    
    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');

    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $this->property_id, $this->item->id);
    JToolBarHelper::custom('unitversions.add', 'plus', '', 'COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT', false);

    //JToolBarHelper::help('', '');
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
    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_AVAILABILITY_EDIT', $this->unit->unit_title, $this->unit->property_id));
    //$document->addScript(JURI::root() . "/administrator/components/com_rental/js/submitbutton.js");
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/availability.js", false, true);
    $document->addStyleSheet(JURI::root() . "media/fc/css/availability.css", 'text/css', "screen");
    JText::script('COM_RENTAL_HELLOWORLD_AVAILABILITY_CHOOSE_START_DATE');
    JText::script('COM_RENTAL_HELLOWORLD_AVAILABILITY_CHOOSE_END_DATE');
  }

}
