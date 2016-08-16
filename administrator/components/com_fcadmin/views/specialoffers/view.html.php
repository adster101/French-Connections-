<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Invoices.
 */
class FcadminViewSpecialOffers extends JViewLegacy
{

  protected $form;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {

    // Get the unit ID we're dealing with...
    $input = JFactory::getApplication()->input;
    $data = $input->get('jform', array(), 'array');
    $this->unit_id = $data['owner'];
    $this->images = array();

    $this->form = $this->get('Form');
    $this->state = $this->get('State');

    $this->addToolbar();

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since	1.6
   */
  protected function addToolbar()
  {
    // Add a back button
    JToolbarHelper::back();
    JToolbarHelper::save('specialoffers.save');

  }

}
