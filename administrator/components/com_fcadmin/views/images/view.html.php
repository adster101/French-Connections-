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
class FcadminViewImages extends JViewLegacy
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
    $this->unit_id = $data['unit_id'];
    $this->images = array();
 		$this->baseURL = JUri::root() . '/images/';
   
    $this->form = $this->get('Form');
    $this->state = $this->get('State');
    
    // Get the image list if we have any
    if (!empty($this->unit_id))
    {
      $folder = JPATH_ROOT . '/images/property/' . $this->unit_id;
      $model = $this->getModel();
      $model->setState('folder', $folder);
      

      $this->images = $this->get('List');
    }

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


    jimport('frenchconnections.toolbar.button.fcstandard');

    $bar = JToolbar::getInstance('images');

    $bar->appendButton('FcStandard', 'image', 'COM_FCADMIN_FETCH_IMAGES', 'images.list', 'btn btn-primary', false);
  }
	function setImage($index = 0)
	{
		if (isset($this->images[$index]))
		{
			$this->_tmp_img = &$this->images[$index];
		}
		else
		{
			$this->_tmp_img = new JObject;
		}
	}
}
