<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class ShortlistController extends JControllerLegacy {

  public function display($cachable = false, $urlparams = array()) {

    $input = JFactory::getApplication()->input;


    $user = JFactory::getUser();

      $safeurlparams = array(
        'id' => 'INT',
        'lang' => 'CMD',
        'user_id' => 'INT'
    );

    // Set the default view name and format from the Request.
    $viewName = $input->get('view', 'shortlist', 'word');
    $input->set('view', $viewName);
    parent::display($cachable, $safeurlparams);

    return $this;
  }

}
