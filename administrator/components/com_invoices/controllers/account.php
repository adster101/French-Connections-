<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Invoice controller class.
 */
class InvoicesControllerAccount extends JControllerForm {

  public function __construct($config = array()) {
    parent::__construct($config);
    
    $this->view_list = 'invoices';
    
  }

  public function allowEdit($data = array(), $key = 'id') {

    $user = JFactory::getUser();

    $input = JFactory::getApplication()->input;

    $user_id = $input->get('user_id', '', 'int');

    if ($user->id == $user_id) {

      return true;
    }

    return false;
  }

}