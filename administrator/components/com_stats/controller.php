<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of the Stats component
 */
class StatsController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'Stats'));
		// call parent behavior

    parent::display($cachable);
	}
 

}
