<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class RealEstateControllerProperty extends JControllerForm
{
  
  protected function postSaveHook(\JModelLegacy $model, $validData = array())
  {
    // Just redirect back to the listings view.
    $this->setRedirect('index.php?option=com_realestate');
  }
  

}
