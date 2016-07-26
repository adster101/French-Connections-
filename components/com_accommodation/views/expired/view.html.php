<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewExpired extends JViewLegacy
{

  // Overwriting JView display method
  function display($tpl = null)
  {

    // Assign data to the view
    $app = JFactory::getApplication();

    $app->setHeader('status', 410, true);

    // Set the default model to the listing model
    $this->setModel(JModelLegacy::getInstance('Listing', 'AccommodationModel'), true);


    $model = $this->getModel();

    // Set Preview property to true so we get inactive props as well in case expired property has
    // been marked as inactive for some reason.
    $model->preview = true;

    if (!$this->item = $model->getItem(true))
    {
      throw new Exception(JText::_('WOOT'), 500);
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
  protected function setDocument()
  {
    $this->title = JText::sprintf('COM_ACCOMMODATION_PROPERTY_EXPIRED_TITLE', $this->item->unit_title);
    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);
    $this->document->setMetaData('robots', 'noindex');
  }
}
