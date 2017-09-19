<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class ShortlistViewShortlist extends JViewLegacy {

  protected $state;
  protected $items;

  // Overwriting JView display method
  function display($tpl = null) {


    $this->state = $this->get('State');
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');

    // Set the document
    //$this->setDocument();
    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {

  }

}
