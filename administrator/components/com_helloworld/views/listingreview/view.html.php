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
class HelloWorldViewListingreview extends JViewLegacy {

  /**
   * Method to display the view.
   *
   * @param   string  $tpl  A template file to load. [optional]
   *
   * @return  void
   *
   * @since   2.5
   */
  public function display($tpl = null) {

    $this->id = JFactory::getApplication()->input->get('property_id', '', 'int');

    // Get the input
    $input = JFactory::getApplication()->input;

    // Determine the layout
    $unitId = $input->get('unit_id', '', 'int');

    $layout = $this->getLayout();

    if ($layout == 'approve') {

      $this->form = $this->get('Form');
    } else {

      /*
       * Get the unit list for this property
       */
      $this->setModel(JModelLegacy::getInstance('Listing', 'HelloWorldModel', array('ignore_request' => true)));
      $model = $this->getModel('Listing');
      $model->setState('com_helloworld.listing.id', $this->id);
      $this->units = $model->getItems();

      /*
       *  Get the appropriate diffs based on whether we have a unit ID or not 
       */
      $this->versions = $this->get('ListingDiff');

      /*
       * If the new property version is empty then there is no change for this property
       */
      $this->property_review = (empty($this->versions['property'][1])) ? 0 : 1;
    }
    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
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
  protected function addToolBar() {

    /*
     * Get the layout we are dealing with
     */
    $layout = $this->getLayout();

    if ($layout == 'approve') {

      JToolBarHelper::title(JText::sprintf('COM_HELLOWORLD_HELLOWORLD_APPROVE_CHANGES', $this->id));
      JToolBarHelper::custom('listing.publish', 'publish', 'publish', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_APPROVE', false);
      
    } else {

      JToolBarHelper::title(JText::sprintf('COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY', $this->id));
      JToolBarHelper::back('Back to property list', 'index.php?option=com_helloworld');
      JToolBarHelper::custom('listing.approve', 'publish', 'publish', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_APPROVE', false);
      JToolBarHelper::custom('listing.reject', 'unpublish', 'unpublish', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_REJECT', false);
      JToolBarHelper::custom('listing.release', 'locked', 'locked', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_CHECKIN', false);
      JToolBarHelper::custom('listing.view', 'edit', 'edit', 'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON', false);
    }
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    
  }

}
