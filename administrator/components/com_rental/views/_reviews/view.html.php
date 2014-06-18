<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewReviews extends JViewLegacy {

  protected $state;

  /**
   * HelloWorlds view display method
   * @return void
   */
  function display($tpl = null) {

    // Add the reviews language file
    JFactory::getLanguage()->load('com_reviews');

    // Add the include path for the reviews model
    $this->addTemplatePath(JPATH_ADMINISTRATOR . '/components/com_reviews/views/reviews/tmpl');

    // Add the reviews model folder to the include path
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_reviews/models');

    // Set the default model
    $this->setModel(JModelLegacy::getInstance('Reviews', 'ReviewsModel'), true);

    // Add the unitversions model so we can grab the unit details...
    $this->setModel(JModelLegacy::getInstance('UnitVersions', 'RentalModel'));

    // Add the property model so we can grab the progress
    $this->setModeL(JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true)));


    // Get special offers for this property by calling getItems method of the model
    $this->items = $this->get('Items');

    // Get the pagination an state...
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');

    // Get an instance of the unitversions model
    $unit = $this->getModel('UnitVersions');

    // Load the unit detail
    $this->unit = $unit->getItem();

    // Get an instance of the listing model
    $listing = $this->getModel('Listing');

    $listing_id = ($this->unit->property_id) ? $this->unit->property_id : '';


    // Set some model options
    $listing->setState('com_rental.' . $listing->getName() . '.id', $listing_id);

    // Get the unit progress...
    $this->progress = $listing->getItems();


    // Register the Helloworld helper file
    JLoader::register('ReviewsHelper', JPATH_ADMINISTRATOR . '/components/com_reviews/helpers/reviews.php');






    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
      die;
    }

    // Set the toolbar
    $this->addToolBar();

    //$this->addTemplatePath(JPATH_ADMINISTRATOR . '/components/com_reviews/views/reviews/tmpl');
    // Display the template
    parent::display($tpl);

    // Set the document
    $this->setDocument();
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {
    // Get component level permissions
    $canDo = RentalHelper::getActions();

    // Determine the view we are using.
    $view = strtolower(JRequest::getVar('view'));

    // Show a helpful toobar title
    JToolBarHelper::title(JText::_('COM_RENTAL_REVIEWS_VIEW'));

    JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CANCEL');

  
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_RENTAL_OFFERS_MANAGE_OFFERS'));
  }

}
