<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewEnquiry extends JViewLegacy {

  // Overwriting JView display method
  function display($tpl = null) {

    // Assign data to the view   
    $app = JFactory::getApplication();

    // If the enquiry data is not set just redirect to the homepage.
    // TO DO - Set it to redirect to the property page...
    if (!$app->getUserState('com_accommodation.enquiry.data'))
    {
      $app->redirect('/');
    }

    // Set the default model to the listing model
    $model = $this->setModel(JModelLegacy::getInstance('Listing', 'AccommodationModel'), true);

    if (!$this->item = $this->get('Item'))
    {
      throw new Exception(JText::_('WOOT'), 410);
    }

    // Get the location breadcrumb trail
    $this->crumbs = $this->get('Crumbs');

    // Get nearby props that are also of interest
    $this->related = $this->get('RelatedProps');
    
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
   * TO DO - Move this into the model?
   *
   * @return void
   */
  protected function setDocument() {

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
    $this->document->setMetaData('robots', 'noindex');

    $document->addScript("media/fc/js/jquery.flexslider.js", 'text/javascript', true, false);
    $document->addScript("media/fc/js/general.js", 'text/javascript', true, false);
    $document->addScript("media/fc/js/property.js", 'text/javascript', true, false);
    $document->addScript("media/fc/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true, false);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/availability.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "media/fc/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }

}
