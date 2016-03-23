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

  /**
   * 
   * allowEdit - overloaded method to allow for a permissions check.
   * If user does not 'own' this invoice then they are not allowed to view
   * 
   * @param type $data
   * @param type $key
   * @return type boolean
   */
  protected function allowEdit($data = array(), $key = 'property_id')
  {

    // Get the invoice detail from the invoice id.
    $model = $this->getModel('Invoice', 'InvoicesModel', array('ignore_request'=>false));
    $items = $model->getItems();

    $recordId = (int) !empty($items[0]->property_id) ? $items[0]->property_id : 0;
    
    // Check the user has access to this record
    return PropertyHelper::allowEditRental($recordId, $this->option);
  }

}