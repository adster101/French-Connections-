<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewCaption extends JViewLegacy
{

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null)
  {

    $this->item = $this->get('Item');
    $this->form = $this->get('Form');

    $this->addToolBar(); 
    

    // Display the template
    parent::display($tpl);
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {



    $bar = JToolbar::getInstance('actions');

    // We can save the new record
    $bar->appendButton('Standard', 'save', 'COM_RENTAL_HELLOWORLD_SAVE_CAPTION', 'images.updatecaption', false);
  }

}
