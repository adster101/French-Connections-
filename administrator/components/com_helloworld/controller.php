<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of HelloWorld component
 */
class HelloWorldController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'HelloWorlds'));
		// call parent behavior
		parent::display($cachable);
	}

	function changeLanguage()
	{	
		$id  	 = JRequest::getInt('id');
		$tabposition = JRequest::getInt('tabposition');
		$tab		 = JRequest::getInt('tab',0);
		$tab 		 = $tabposition ? '&tab='.$tab : '';
		$session 	 =& JFactory::getSession();
		$session->set('com_helloworld.property.'.$id.'.lang', JRequest::getVar('Language'));
		$view = JRequest::getVar('view');
		$this->setRedirect('index.php?option=com_helloworld&task='.$view.'.edit&id='.$id);
	}	
}
