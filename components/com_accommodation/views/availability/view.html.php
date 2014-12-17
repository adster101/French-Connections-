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
    $model = JModelLegacy::getInstance('Listing','AccommodationModel');
    $this->setModel($model, true);

    $app = JFactory::getApplication();

    if (!$this->item = $this->get('Item'))
    {
      throw new Exception(JText::_('EXPIRED'), 410);
    }

    // Get the availability for this property
    $this->availability = $this->get('Availability');


    $model->hit();

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseWarning(404, implode("\n", $errors));
      return false;
    }

    // Configure the pathway.
    if (!empty($this->crumbs))
    {
      $app->getPathWay()->setPathway($this->crumbs);
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

    $document = JFactory::getDocument();

    if ($this->item->accommodation_type == 'Bed and Breakfast')
    {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_BED_AND_BREAKFAST_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->city, $this->item->department);
    }
    else
    {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_SELF_CATERING_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->city, $this->item->department);
    }

    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);

    //$document->addScript("media/fc/js/jquery.flexslider.js", 'text/javascript', true, false);
    //$document->addScript("media/fc/js/general.js", 'text/javascript', true, false);
    //$document->addScript("media/fc/js/property.js", 'text/javascript', true, false);
    //$document->addScript("media/fc/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true, false);
    //$document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/availability.css", 'text/css', "screen");
    //$document->addStyleSheet(JURI::root() . "media/fc/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }

}
