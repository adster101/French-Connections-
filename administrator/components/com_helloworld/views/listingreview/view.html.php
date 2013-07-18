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
class HelloWorldViewListingReview extends JViewLegacy {

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
    
    $this->id = JFactory::getApplication()->input->get('parent_id','','int');
    
    // Get the input
    $input = JFactory::getApplication()->input;

    // Determine the layout
    $unitId = $input->get('unit_id', '', 'int');

    // Get the appropriate diffs based on whether we have a unit ID or not - could use a set method in the controller perhaps?
    $this->item = (!$unitId) ? $this->get('PropertyDiff') : $this->get('UnitDiff');
    
    $this->units = $this->get('Units');

    $this->addToolbar();

    $this->setDocument();

    parent::display($tpl);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar() {

    JToolBarHelper::title(JText::sprintf('COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY', $this->id));

    JToolBarHelper::cancel('listings', 'JTOOLBAR_CANCEL');
    JToolBarHelper::custom('listing.approve', 'publish', 'publish', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_APPROVE');
    JToolBarHelper::custom('listing.reject', 'unpublish', 'unpublish', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_REJECT');
    JToolBarHelper::custom('listing.checkin', 'locked', 'locked', 'COM_HELLOWORLD_HELLOWORLD_REVIEW_PROPERTY_CHECKIN');
    JToolBarHelper::preview('http://dev.frenchconnections.co.uk/index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->id . '&unit_id=' . (int) $this->units[0]->unit_id);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    
  }

}
