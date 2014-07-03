<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Invoice controller class.
 */
class InvoicesControllerImport extends JControllerForm {

  public function __construct($config = array()) {
    parent::__construct($config);
    
    $this->view_list = 'invoices';
    
  }
}