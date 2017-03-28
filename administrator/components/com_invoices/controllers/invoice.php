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

    // Get the invoice account owner detail from the invoice id.
    $model = $this->getModel('Invoice', 'InvoicesModel', array('ignore_request'=>false));
    $items = $model->getItems();
    // The account ID of the owner the invoice was raised against
    $ownerId = (int) !empty($items[0]->user_id) ? $items[0]->user_id : 0;

    // The currently logged in user
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Check general edit permission first.
    if ($user->authorise('core.edit', $this->option))
    {
      return true;
    }

    // If we don't have an owner on the invoice then we can't authorise
    if ($ownerId === 0)
    {
      return false;
    }

    // If the user has 'edit' e.g. view permission on this item check that the
    // current user matches the owner the invoice was raised against
    if ($user->authorise('core.edit.own', $this->option))
    {
      // If the invoice owner matches 'the owner' then it's all good
      if ($ownerId == $userId)
      {
        return true;
      }
    }

    return false;

  }

}
