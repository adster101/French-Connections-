<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewListing extends JViewLegacy {

  // Overwriting JView display method
  function display($tpl = null) {

    // TODO - Here we should add the relevant admin model and move 
    // getAvailability
    // getTariffs
    // getImages
    // getFacilities
    // getCrumbs
    // getReviews
    // getUnits
    // to the relevant admin model. These methods should then be reused across the review, preview and listing views.
    // Assign data to the view
    
    $app = JFactory::getApplication();

    if (!$this->item = $this->get('Item')) {

      throw new Exception(JText::_('WOOT'), 410);
    }

    // Get the availability for this property
    $this->availability = $this->get('AvailabilityCalendar');

    // Get the tariffs for this property
    $this->tariffs = $this->get('Tariffs');

    // Get the tariffs for this property
    $this->images = $this->get('Images');

    // Get the unit facilities for this property
    $this->unit_facilities = $this->get('UnitFacilities');

    // Get the proprety facilities for this property
    $this->property_facilities = $this->get('PropertyFacilities');

    // Get the location breadcrumb trail
    $this->crumbs = $this->get('Crumbs');

    // Get the reviews
    $this->reviews = $this->get('Reviews');

    // Get the unit info so we can show any units present...
    $this->units = $this->get('Units');

    // Get the enquiry form
    $this->form = $this->get('Form');

    // Get the special offer is one is current
    $this->offer = $this->get('Offers');
    
    // Get the current list of shortlisted properties for this user
    $this->shortlist = $this->get('Shortlist');

    // Check the expiry date here. If not valid throw an error with a 403 code?
    // Get component params
    // Think of some params to store for this component?
    // Update the hit counter for this view
    $model = $this->getModel();
    $model->hit();

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseWarning(404, implode("\n", $errors));
      return false;
    }

    // Configure the pathway.
    if (!empty($this->crumbs)) {
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
  protected function setDocument() {

    $document = JFactory::getDocument();

    if ($this->item->accommodation_type == 'Bed and Breakfast') {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_BED_AND_BREAKFAST_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->city, $this->item->department);
    } else {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_SELF_CATERING_TITLE', $this->item->unit_title, $this->item->property_type, $this->item->city, $this->item->department);
    }

    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);
  }

}
