<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Admin Component Help Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
class AdminModelHelp extends JModelLegacy
{
	/**
	 * @var string the search string
	 */
	protected $help_search = null;

	/**
	 * @var string the page to be viewed
	 */
	protected $page = null;

	/**
	 * @var string the iso language tag
	 */
	protected $lang_tag = null;

	/**
	 * @var array Table of contents
	 */
	protected $toc = null;

	/**
	 * @var string url for the latest version check
	 */
	protected $latest_version_check = null;

	/**
	 * Method to get the help search string
	 * @return string Help search string
	 */
	public function &getHelpSearch()
	{
		if (is_null($this->help_search)) {
			$this->help_search = JFactory::getApplication()->input->getString('helpsearch');
		}
		return $this->help_search;
	}

	/**
	 * Method to get the page
	 * @return string page
	 */
	public function &getPage()
	{
		if (is_null($this->page))
		{
			$page = JFactory::getApplication()->input->get('page', 'JHELP_START_HERE');
			$this->page = JHelp::createUrl($page);
		}
		return $this->page;
	}

	/**
	 * Method to get the lang tag
	 *
	 * @return  string  lang iso tag
	 */
	public function getLangTag()
	{
		if (is_null($this->lang_tag))
		{
			$lang = JFactory::getLanguage();
			$this->lang_tag = $lang->getTag();

			if (!is_dir(JPATH_BASE . '/help/' . $this->lang_tag))
			{
				// Use english as fallback
				$this->lang_tag = 'en-GB';
			}

		}
		return $this->lang_tag;
	}

	/**
	 * Method to get the toc
	 * @return array Table of contents
	 */
	public function &getToc()
	{
		if (is_null($this->toc))
		{
			// Get vars
			$lang_tag = $this->getLangTag();
			$help_search = $this->getHelpSearch();

			// Get Help files
			jimport('joomla.filesystem.folder');
			$files = JFolder::files(JPATH_BASE . '/help/' . $lang_tag, '\.xml$|\.html$');
			$this->toc = array();
			foreach($files as $file)
			{
				$buffer = file_get_contents(JPATH_BASE . '/help/' . $lang_tag . '/' . $file);
				if (preg_match('#<title>(.*?)</title>#', $buffer, $m))
				{
					$title = trim($m[1]);
					if ($title) {
						// Translate the page title
						$title = JText::_($title);
						// strip the extension
						$file = preg_replace('#\.xml$|\.html$#', '', $file);
						if ($help_search)
						{
							if (JString::strpos(JString::strtolower(strip_tags($buffer)), JString::strtolower($help_search)) !== false) {
								// Add an item in the Table of Contents
								$this->toc[$file] = $title;
							}
						}
						else
						{
							// Add an item in the Table of Contents
							$this->toc[$file] = $title;
						}
					}
				}
			}
			// Sort the Table of Contents
			asort($this->toc);
		}
		return $this->toc;
	}

	/**
	 * Method to get the latest version check;
	 * @return string Latest Version Check URL
	 */
	public function &getLatestVersionCheck()
	{
		if (!$this->latest_version_check) {
			$override = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:Joomla_Version_{major}_{minor}_{maintenance}';
			$this->latest_version_check = JHelp::createUrl('JVERSION', false, $override);
		}
		return $this->latest_version_check;
	}

}
