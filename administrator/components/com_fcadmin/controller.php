<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class FcadminController extends JControllerLegacy
{

  /**
   * display task
   *
   * @return void
   */
  public function display($cachable = false, $urlparams = array())
  {
    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'fcadmin'));
    // call parent behavior
    parent::display($cachable);
  }

}
