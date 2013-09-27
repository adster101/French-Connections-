<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class RegisterOwnerViewRegisterowner extends JViewLegacy {

  protected $state;
  protected $form;
  protected $item;
  protected $return_page;

// Overwriting JView display method
  function display($tpl = null) {


    $this->state = $this->get('State');

    $this->form = $this->get('Form');


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
    
  }

}
