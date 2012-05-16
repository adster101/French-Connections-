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
	 //$trace = debug_backtrace();
   	// $file   = $trace[1]['file'];
   	// $line   = $trace[1]['line'];
  	// $object = $trace[1]['object'];
    	//if (is_object($object)) { $object = get_class($object); }
	//echo "Where called: line $line of $object \n(in $file)";



		$id  	 = JRequest::getInt('id');
		$tabposition = JRequest::getInt('tabposition');
		$tab		 = JRequest::getInt('tab',0);
		$tab 		 = $tabposition ? '&tab='.$tab : '';
		$session 	 =& JFactory::getSession();
		$session->set('com_helloworld.property.'.$id.'.lang', JRequest::getVar('Language'));

		$this->setRedirect('index.php?option=com_helloworld&task=helloworld.edit&id='.$id);
	}	
}
