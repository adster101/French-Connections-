<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of HelloWorld component
 */
class EnquiriesController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{

		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'Enquiries'));
		
    $view   = $this->input->get('view', 'enquiries');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'enquiry' && $layout == 'edit' && !$this->checkEditId('com_enquiries.edit.enquiry', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_enquiries', false));

			return false;
		}
    
    // call parent behavior
    parent::display($cachable); 

	}
 

}
