<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class fc_redirectController extends JControllerLegacy {

  public function display($cachable = false) {
    
    parent::display($cachable);

    return $this;
  }

}
