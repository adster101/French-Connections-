<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Indexer view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class RealEstateViewReview extends JViewLegacy
{

  /**
   * Method to display the view.
   *
   * @param   string  $tpl  A template file to load. [optional]
   *
   * @return  void
   *
   * @since   2.5
   */
  public function display($tpl = null)
  {

    // Get the input
    $input = JFactory::getApplication()->input;
    $this->id = $input->get('property_id', '', 'int');
    $layout = $this->getLayout();



    if ($layout == 'approve' || $layout == 'reject')
    {
      $this->form = $this->get('Form');
    }
    else
    {
      // Get the listing model which returns the list of units that make up a listing
      $this->setModel(JModelLegacy::getInstance('Listing', 'RealEstateModel', array('ignore_request' => true)));
      $model = $this->getModel('Listing');
      $model->setState('com_realestate.listing.id', $this->id);
      $this->units = $model->getItems();

      // Get the appropriate diffs based on whether we have a unit ID or not 
      $this->versions = $this->get('ListingDiff');

      // If the new property version is empty then there is no change for this property
      $this->property_review = (empty($this->versions['property'][1])) ? 0 : 1;
    }

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    $this->addToolbar();
    $this->setDocument();

    parent::display($tpl);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    /*
     * Get the layout we are dealing with
     */
    $layout = $this->getLayout();

    if ($layout == 'approve')
    {

      JToolBarHelper::title(JText::sprintf('COM_RENTAL_HELLOWORLD_APPROVE_CHANGES', $this->id));
      JToolBarHelper::back();

      JToolBarHelper::custom('listing.publish', 'publish', 'publish', 'COM_RENTAL_REVIEW_PROPERTY_SEND_APPROVAL_EMAIL', false);
    }
    elseif ($layout == 'reject')
    {

      JToolBarHelper::title(JText::sprintf('COM_RENTAL_REJECT_CHANGES', $this->id));
      JToolBarHelper::back();

      JToolBarHelper::custom('listing.decline', 'publish', 'publish', 'COM_RENTAL_REVIEW_PROPERTY_SEND_REJECTION_EMAIL', false);
    }
    else
    {

      JToolBarHelper::title(JText::sprintf('COM_RENTAL_HELLOWORLD_REVIEW_PROPERTY', $this->id));
      JToolBarHelper::back('Back to property list', 'index.php?option=com_rental');
      JToolBarHelper::custom('listing.approve', 'publish', 'publish', 'COM_RENTAL_HELLOWORLD_REVIEW_PROPERTY_APPROVE', false);
      JToolBarHelper::custom('listing.reject', 'unpublish', 'unpublish', 'COM_RENTAL_HELLOWORLD_REVIEW_PROPERTY_REJECT', false);
      JToolBarHelper::custom('listing.release', 'locked', 'locked', 'COM_RENTAL_HELLOWORLD_REVIEW_PROPERTY_CHECKIN', false);
      JToolBarHelper::custom('listing.view', 'edit', 'edit', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON', false);
      // Get a toolbar instance so we can append the preview button

      $bar = JToolBar::getInstance('toolbar');
      $property_id = $this->units[0]->id;
      $unit_id = $this->units[0]->unit_id;
      $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $property_id, $unit_id);
    }
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    
  }

}
