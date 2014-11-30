<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class FcadminHelper {

  /**
   * Configure the Linkbar.
   */
  public static function addSubmenu($submenu) {
    // Get the ID of the item we are editing
    $id = JRequest::getVar('id');
    
    JHtmlSidebar::addEntry(JText::_('Menu'), '#');
    JHtmlSidebar::addEntry(JText::_('Admin'), 'index.php?option=com_fcadmin', $submenu == 'fcadmin');
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_IMPORT_INVOICES'), 'index.php?option=com_fcadmin&view=myobsync', $submenu == 'myobsync');
    
  }

}
