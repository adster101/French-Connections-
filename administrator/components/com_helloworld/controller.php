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
    // Set up an array of views to protect from direct access
    $views_to_protect = array('helloworld' => 1, 'availability' => 1, 'images' => 1, 'tariffs' => 1, 'offers' => 1);
    
    // Get the document object.
		$document = JFactory::getDocument();
    
    // set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'HelloWorlds'));
    
 		// Set the default view name and format from the Request.   
    $vName		= JRequest::getCmd('view', 'helloworlds');
		$lName		= JRequest::getCmd('layout', 'default');
		$id			= JRequest::getInt('id');

		// Check for edit form. This checks that the edit ID is set in the session.
    // This only occurs when someone follows a link ?option=com_helloworld&task=helloworld.edit&id=78
    // A check in each sub controller is also needed to ensure that the user does actually own the item id
		if (array_key_exists($vName, $views_to_protect) && $lName == 'edit' && !$this->checkEditId('com_helloworld.edit.' .$vName , $id)) {
      
      // Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=helloworlds', false));

			return false;
		}
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
