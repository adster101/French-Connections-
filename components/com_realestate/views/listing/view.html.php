<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class RealestateViewListing extends JViewLegacy
{

  // Overwriting JView display method
  function display($tpl = null)
  {

    // TODO - Here we should add the relevant admin model and move
    // getImages
    // getCrumbs
    // to the relevant admin model. These methods should then be reused across the review, preview and listing views.
    // Assign data to the view
    $app = JFactory::getApplication();

    if (!$this->item = $this->get('Item'))
    {
      // Get the URI
      $uri = JURI::getInstance();

      // Get the query string
      $query = $uri->getQuery(true);

      // Append the view
      $query['view'] = 'expired';

      // Set the query string
      $uri->setQuery($query);

      // And redirect
      header('Location: ' . $uri->toString());
      exit;
    }

    // Get the images for this property
    $this->images = $this->get('Images');

    // Get the location breadcrumb trail
    $this->crumbs = $this->get('Crumbs');

    // Get the enquiry form
    $this->form = $this->get('Form');

    // Get the current list of shortlisted properties for this user
    $this->shortlist = SearchHelper::getShortlist();

    // Update the hit counter for this view
    $model = $this->getModel();
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

    $this->title = JText::sprintf('COM_REALESTATE_PROPERTY_TITLE', $this->item->title);

    // Set document and page titles
    $this->document->setTitle($this->title);
    $this->document->setDescription($this->title);
    $this->document->setMetaData('keywords', $this->title);
  }

}
