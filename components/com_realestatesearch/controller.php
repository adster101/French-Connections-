<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


/**
 * Finder Component Controller.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class RealestateSearchController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached. [optional]
	 * @param   array    $urlparams  An array of safe url parameters and their variable types,
	 *                               for valid values see {@link JFilterInput::clean()}. [optional]
	 *
	 * @return  JControllerLegacy  This object is to support chaining.
	 *
	 * @since   2.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;
		$cachable = false;

    // Set the default view name and format from the Request.
		$viewName = $input->get('view', 'search', 'word');
		$input->set('view', $viewName);

		// Don't cache view for search queries
		if ($input->get('q') || $input->get('f') || $input->get('t'))
		{
			$cachable = false;
		}

		$safeurlparams = array(
			's_kwds' 	=> 'CMD',
      'start' => 'CMD',  
      'limitstart' => 'CMD',  
      'bedrooms' => 'CMD',  
      'order' => 'CMD',  
      'min' => 'CMD',
      'max' => 'CMD'  
		);

		return parent::display($cachable, $safeurlparams);
	}
}
