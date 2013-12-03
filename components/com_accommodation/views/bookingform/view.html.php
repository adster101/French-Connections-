<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewBookingform extends JViewLegacy {

  // Overwriting JView display method
  function display($tpl = null) {

    // Set default model to ListingModel
    $model = $this->setModel(JModelLegacy::getInstance('Listing', 'AccommodationModel'), true);

    // Get the item detail
    $this->item = $this->get('Item');

    // Check for errors.
    $errors = $this->get('Errors');

    if (count($errors) > 0) {
      // Generate a logger instance for reviews
      JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('General'));
      JLog::add('There was a problem fetching listing details for - ' . $this->item->id . ')' . implode('<br />', $errors), JLog::ALL, 'General');
      JError::raiseError(500, 'Problem loading property details. Error has been logged. Please contact us if this problem persists.');
      return false;
    }

    // Set the document
    $this->setDocument();

    // Display the view
    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {

    $document = JFactory::getDocument();

    if ($this->item->accommodation_type == 'Bed and Breakfast') {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_BED_AND_BREAKFAST_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->accommodation_type, $this->item->city, $this->item->department);
    } else {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_SELF_CATERING_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->accommodation_type, $this->item->city, $this->item->department);
    }

  }

}
