<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class NotesController extends JControllerLegacy
{

  
  /**
   * display task
   *
   * @return void
   */
	public function display($cachable = false, $urlparams = array())
  {

    // Set the default view for this component
    JRequest::setVar('view', JRequest::getCmd('view', 'notes'));
    
    // call parent behavior
    parent::display($cachable);
  }
}
