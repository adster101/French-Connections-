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

/**
 * Invoices helper.
 */
class TicketsHelper {

  public static function addSubmenu($vName = '') {
    $user = JFactory::getUser();
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_OWNERS_AREA_HOME'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_HELLOWORLD_SUBMENU_OWNERS_HOME'), 'index.php');
    JHtmlSidebar::addEntry(JText::_('COM_TICKETS_TICKETS_SUBMENU_MENU'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_TICKETS_TICKETS_SUBMENU_TICKETS'), 'index.php?option=com_tickets&view=tickets', $vName == 'tickets');
    JHtmlSidebar::addEntry(JText::_('COM_TICKETS_TICKETS_SUBMENU_TICKET_TYPE'), 'index.php?option=com_categories&extension=com_tickets', $vName == 'categories');
  }

  /**
   * Gets a list of the actions that can be performed.
   *
   * @return	JObject
   * @since	1.6
   */
  public static function getActions() {
    $user = JFactory::getUser();
    $result = new JObject;

    $assetName = 'com_tickets';

    $actions = array(
        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
    );

    foreach ($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }

  /**
   * Get a list of filter options for the state of a module. As below, used mainly to override default labels.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getSeverities() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_TICKETS_CRITICAL'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_TICKETS_HIGH'));
    $options[] = JHtml::_('select.option', '3', JText::_('COM_TICKETS_MEDIUM'));
    $options[] = JHtml::_('select.option', '4', JText::_('COM_TICKETS_LOW'));
    $options[] = JHtml::_('select.option', '5', JText::_('COM_TICKETS_MINOR'));
    return $options;
  }

  /**
   * Get a list of filter options for the state of a module. As below, used mainly to override default labels.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getStateOptions() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_TICKETS_OPEN'));
    $options[] = JHtml::_('select.option', '0', JText::_('COM_TICKETS_CLOSED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_TICKETS_TESTING_FILTER'));
    $options[] = JHtml::_('select.option', '3', JText::_('COM_TICKETS_PENDING_FILTER'));
    return $options;
  }
}
