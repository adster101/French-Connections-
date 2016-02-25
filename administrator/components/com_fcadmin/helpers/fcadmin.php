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
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_NO_AVAILABILITY'), 'index.php?option=com_fcadmin&view=noavailability&format=raw', $submenu == 'noavailability');
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_WHERE_HEARD'), 'index.php?option=com_fcadmin&view=whereheard&format=raw', $submenu == 'whereheard');
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_IMAGES'), 'index.php?option=com_fcadmin&view=images', $submenu == 'images');
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_NOTIFICATIONS'), 'index.php?option=com_fcadmin&view=notification', $submenu == 'notification');
    JHtmlSidebar::addEntry(JText::_('COM_FCADMIN_NOPROPERTY'), 'index.php?option=com_fcadmin&view=noproperty&format=raw', $submenu == 'noproperty');
  }

}
