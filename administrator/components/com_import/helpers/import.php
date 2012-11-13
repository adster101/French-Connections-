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
    JHtmlSidebar::addEntry(JText::_('Import users'), 'index.php?option=com_import&view=users', $submenu == 'users');
		JHtmlSidebar::addEntry(JText::_('Import properties'), 'index.php?option=com_import&view=properties', $submenu == 'properties');	
		JHtmlSidebar::addEntry(JText::_('Import attributes'), 'index.php?option=com_import&view=propertyattributes', $submenu == 'propertyattributes');	
		JHtmlSidebar::addEntry(JText::_('Import availability'), 'index.php?option=com_import&view=availability', $submenu == 'availability');	
  }
}
