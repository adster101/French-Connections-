<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewProperty extends JViewLegacy {

  // Overwriting JView display method
  function display($tpl = null) {
    
    // Assign data to the view
    $this->item = $this->get('Item');

    //$this->facilities = $this->get('Facilities');
    // Get the availability for this property
    $this->availability = $this->get('Availability');

    // Get the tariffs for this property
    $this->tariffs = $this->get('Tariffs');

    // Get the tariffs for this property
    $this->images = $this->get('Images');

    // Get the facilities for this property
    $this->facilities = $this->get('Facilities');

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
    
    // Get component params
    // Think of some params to store for this component?
    
    // Update the hit counter for this view
    $model = $this->getModel();
		$model->hit(); 
    
    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
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
    
    if ($this->facilities['Accommodation Type'][0] == 'Bed and Breakfast') {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_BED_AND_BREAKFAST_TITLE', $this->item->title, $this->facilities['Property Type'][0], $this->facilities['Accommodation Type'][0], $this->item->nearest_town, $this->item->department_as_text);
    } else {
      $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_SELF_CATERING_TITLE', $this->item->title, $this->facilities['Property Type'][0], $this->facilities['Accommodation Type'][0], $this->item->nearest_town, $this->item->department_as_text);
    }
    
    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);

    $document->addScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");
    $document->addScript("http://s7.addthis.com/js/250/addthis_widget.js#pubid=frenchconnections", 'text/javascript', true, true);
    $document->addScript("components/com_accommodation/js/jquery.flexslider-min.js", 'text/javascript', true, false);
    $document->addScript("components/com_accommodation/js/property.js", 'text/javascript', true, false);
    $document->addScript("media/fc/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true, false);
    $document->addScript("media/fc/js/date-range.js", 'text/javascript', true, false);
    $document->addScript("media/system/js/mootools-more.js", 'text/javascript', false, false);
    $document->addStyleSheet(JURI::root() . "components/com_accommodation/css/styles.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/availability.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "components/com_accommodation/css/flexslider.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "media/fc/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }
}
