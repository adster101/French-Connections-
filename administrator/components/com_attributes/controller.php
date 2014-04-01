<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of HelloWorld component
 */
class AttributesController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'Attributes'));
		// call parent behavior
		parent::display($cachable);
	}
  
  /*
   * function changeLanguage - Sets the language in the session state
   * used in various component views to load translations for editing
   * 
   * TO DO: Wrap into a help function so that this is reusable
   * 
   */
	function changeLanguage()
	{	
		$id  	 = JRequest::getInt('id');
		$session 	 =& JFactory::getSession();
		$session->set('com_rental.property.'.$id.'.lang', JRequest::getVar('Language'));
		$view = JRequest::getVar('view');
		$this->setRedirect('index.php?option=com_rental&task='.$view.'.edit&id='.$id);
	}	  

}
