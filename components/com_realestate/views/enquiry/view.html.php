<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class RealestateViewEnquiry extends JViewLegacy
{

  // Overwriting JView display method
  function display($tpl = null)
  {

    // Assign data to the view   
    $app = JFactory::getApplication();

    // If the enquiry data is not set just redirect to the homepage.
    // TO DO - Set it to redirect to the property page...
    if (!$app->getUserState('com_realestate.enquiry.data'))
    {
      $app->redirect('/');
    }

    // Set the default model to the listing model
    $model = $this->setModel(JModelLegacy::getInstance('Listing', 'RealestateModel'), true);

    if (!$this->item = $this->get('Item'))
    {
      throw new Exception(JText::_('There has been an error, please try again...'), 500);
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
    $this->title = JText::sprintf('COM_ACCOMMODATION_ENQUIRY_SENT_TITLE', $this->item->title);
    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);
    $this->document->setMetaData('robots', 'noindex');
  }
}
