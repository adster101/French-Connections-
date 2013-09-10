<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class ImportHelper {

  /**
   * Configure the Linkbar.
   */
  public static function addSubmenu($submenu) {
    // Get the ID of the item we are editing
    $id = JRequest::getVar('id');
    JHtmlSidebar::addEntry(JText::_('Import'), 'index.php?option=com_import', $submenu == 'import');
    JHtmlSidebar::addEntry(JText::_('Articles'), 'index.php?option=com_import&view=articles', $submenu == 'articles');
    JHtmlSidebar::addEntry(JText::_('Attributes'), 'index.php?option=com_import&view=propertyattributes', $submenu == 'propertyattributes');
    JHtmlSidebar::addEntry(JText::_('Availability'), 'index.php?option=com_import&view=availability', $submenu == 'availability');
    JHtmlSidebar::addEntry(JText::_('Enquiries'), 'index.php?option=com_import&view=enquiries', $submenu == 'enquiries');
    JHtmlSidebar::addEntry(JText::_('Images'), 'index.php?option=com_import&view=images', $submenu == 'images');
    JHtmlSidebar::addEntry(JText::_('Locations'), 'index.php?option=com_import&view=locations', $submenu == 'locations');
    JHtmlSidebar::addEntry(JText::_('Locations Translations'), 'index.php?option=com_import&view=locationstranslations', $submenu == 'locationstranslations');
    JHtmlSidebar::addEntry(JText::_('Notes'), 'index.php?option=com_import&view=notes', $submenu == 'notes');
    JHtmlSidebar::addEntry(JText::_('Property listings'), 'index.php?option=com_import&view=property_listings', $submenu == 'property_listings');
    JHtmlSidebar::addEntry(JText::_('Property amenities'), 'index.php?option=com_import&view=property_amenities', $submenu == 'property_amenities');
    JHtmlSidebar::addEntry(JText::_('Reviews'), 'index.php?option=com_import&view=reviews', $submenu == 'reviews');
    JHtmlSidebar::addEntry(JText::_('Special offers'), 'index.php?option=com_import&view=specialoffers', $submenu == 'specialoffers');
    JHtmlSidebar::addEntry(JText::_('Tariffs'), 'index.php?option=com_import&view=tariffs', $submenu == 'tariffs');
    JHtmlSidebar::addEntry(JText::_('Users'), 'index.php?option=com_import&view=users', $submenu == 'users');
  }

}
