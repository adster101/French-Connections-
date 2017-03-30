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

jimport('joomla.application.component.controllerform');

/**
 * Invoice controller class.
 */
class TicketsControllerTicket extends JControllerForm
{

  /**
   * Method to run batch operations.
   *
   * @param   object  $model  The model.
   *
   * @return  boolean   True if successful, false otherwise and internal error is set.
   *
   * @since   1.7
   */
  public function batch($model = null)
  {
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Set the model
    $model = $this->getModel('Ticket', '', array());

    // Preset the redirect
    $this->setRedirect(JRoute::_('index.php?option=com_tickets&view=tickets' . $this->getRedirectToListAppend(), false));

    return parent::batch($model);
  }

}