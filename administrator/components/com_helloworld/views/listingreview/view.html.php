<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Indexer view class for Finder.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 * @since       2.5
 */
class HelloWorldViewListingReview extends JViewLegacy
{
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();
		$this->setDocument(); 		

		parent::display($tpl);

	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
    
	}

  /**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		
  }

}
