<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class RentalControllerMarketing extends RentalControllerBase
{
  public function postSaveHook(\JModelLegacy $model, $validData = array())
  {
    // Redirect to the payment view...
    $id = $validData['property_id'];
    $this->setRedirect('index.php?option=com_rental&task=payment.summary&id=' . (int) $id);
    
    
  }
}
