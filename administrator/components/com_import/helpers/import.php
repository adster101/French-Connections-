<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class ImportHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{	
		// Get the ID of the item we are editing
		$id = JRequest::getVar('id');
		JHtmlSidebar::addEntry(JText::_('Import'), 'index.php?option=com_import', $submenu == 'import');
    JHtmlSidebar::addEntry(JText::_('Users'), 'index.php?option=com_import&view=users', $submenu == 'users');
		JHtmlSidebar::addEntry(JText::_('Properties'), 'index.php?option=com_import&view=properties', $submenu == 'properties');	
		JHtmlSidebar::addEntry(JText::_('Attributes'), 'index.php?option=com_import&view=propertyattributes', $submenu == 'propertyattributes');	
		JHtmlSidebar::addEntry(JText::_('Availability'), 'index.php?option=com_import&view=availability', $submenu == 'availability');	
		JHtmlSidebar::addEntry(JText::_('Tariffs'), 'index.php?option=com_import&view=tariffs', $submenu == 'tariffs');	
  }
}
