<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class PlaceofinterestViewPlaceofinterest extends JViewLegacy {

  protected $state;
  protected $form;
  protected $item;
  protected $return_page;

  // Overwriting JView display method
  function display($tpl = null) {

    $this->state = $this->get('State');
    $this->item = $this->get('Item');

    // Set the document
    $this->setDocument();
    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();

    // Set document and page titles
    $this->document->setTitle($this->item->title);
    $this->document->setDescription($this->item->description);
    $this->document->setMetaData('keywords', $this->item->title);

    $document->addScript("media/fc/js/general.js", 'text/javascript', true, false);
  }

}
