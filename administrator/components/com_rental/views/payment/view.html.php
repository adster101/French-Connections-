<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

// import our payment library class
jimport('frenchconnections.models.payment');

/**
 * HelloWorlds View
 */
class RentalViewPayment extends JViewLegacy
{

  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   *
   * @return void
   */
  function display($tpl = null)
  {

    $input = JFactory::getApplication()->input;
    $this->id = $input->get('id', '', 'int');
    $layout = $input->get('layout', '', 'string');
    $this->renewal = $input->getCmd('renewal', false);

    // Get an instance of the Listing model
    $this->setModel(JModelLegacy::getInstance('Listing', 'RentalModel'));
    $model = $this->getModel('Listing');   
    $model->setState('com_rental.listing.latest', true);


    $current_version = $model->getItems();
    
    $model->setState('com_rental.listing.latest', false);
    $previous_version = $model->getItems();
    
    // Add the Property model so we can get the renewal details...
    $listing = JModelLegacy::getInstance('Payment', 'FrenchConnectionsModel', $config = array('listing' => $current_version, 'renewal' => $this->renewal));

    // Get the units and image details they against this property
    $this->summary = $listing->getPaymentSummary($current_version, $previous_version);

    if ($layout == 'account')
    {
      // Get the account form
      $this->form = $this->get('Form');
    }
    elseif ($layout == 'payment')
    {
      // Get the payment form
      $this->form = $this->get('PaymentForm');
    }

    // Set the document
    $this->setDocument();

    // Set the document
    $this->addToolBar();

    // Display the template
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

    // Set the page title
    JToolBarHelper::title(JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY', $this->id));

    $document->addScript(JURI::root() . "/media/fc/js/general.js", false, true);

    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_HELLOWORLD_UNSAVED_CHANGES');
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {
    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

    $document = JFactory::getDocument();
    $document->setTitle(JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY', $this->id));

    //JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CANCEL');

    JToolBarHelper::help('COM_RENTAL_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
  }

}
