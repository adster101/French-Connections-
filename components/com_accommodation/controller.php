<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * Hello World Component Controller
 */

class AccommodationController extends JControllerLegacy
{
  public function display($cachable = true, $urlparams = array()) {
    
  	parent::display($cachable);

    return $this;
  }
}
