<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class AccommodationController extends JControllerLegacy {

  public function display($cachable = true, $urlparams = array()) {
    $input = JFactory::getApplication()->input;

    // Set the default view name and format from the Request.
    $viewName = $input->get('view', 'listing', 'word');
    $input->set('view', $viewName);
    parent::display($cachable);

    return $this;
  }

}
