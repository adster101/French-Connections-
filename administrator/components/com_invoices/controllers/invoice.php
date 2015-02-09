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
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class InvoicesControllerInvoice extends JControllerForm
{

  function __construct()
  {
    parent::__construct();

    $this->view_list = 'invoices';
  }

  protected function allowEdit($data = array(), $key = 'property_id')
  {

    $model = $this->getModel('Invoice', 'InvoicesModel', array('ignore_request'=>false));
    $items = $model->getItems();

    $recordId = (int) !empty($items[0]->property_id) ? $items[0]->property_id : 0;
    
    return PropertyHelper::allowEditRental($recordId, $this->option);
    
  }

}