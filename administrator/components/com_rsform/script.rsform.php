<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class com_rsformInstallerScript
{	
	function postflight($type, $parent)
	{
		if ($type == 'update')
		{
			$sqlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'install.rsform.utf8.sql';
			$buffer = file_get_contents($sqlfile);
			if ($buffer === false)
			{
				JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
				return false;
			}
			
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) == 0) {
				// No queries to process
				return 0;
			}
			
			$db =& JFactory::getDBO();
			
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$db->setQuery($query);
					if (!$db->query())
					{
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return false;
					}
				}
			}
		}
	}
}