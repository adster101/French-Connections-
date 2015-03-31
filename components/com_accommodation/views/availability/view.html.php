<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewAvailability extends JViewLegacy
{

  // Overwriting JView display method
  function display($tpl = null)
  {

    // Get an instance of the Accommodation listing model and set it to the default 
    $model = JModelLegacy::getInstance('Listing', 'AccommodationModel');
    $this->setModel($model, true);

    $app = JFactory::getApplication();

    if (!$this->item = $this->get('Item'))
    {
      throw new Exception(JText::_('EXPIRED'), 410);
    }

    // Get the availability for this property
    $this->availability = $this->get('Availability');

    // Get the tariffs...
    $this->tariffs = $this->get('Tariffs');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseWarning(404, implode("\n", $errors));
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
  protected function setDocument()
  {
    // Set the title
    $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_AVAILABILITY_AND_TARIFFS', $this->item->unit_title);

    // Set document and page titles
    $this->document->setTitle($this->title);

  }

}
