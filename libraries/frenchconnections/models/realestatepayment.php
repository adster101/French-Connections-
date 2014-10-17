<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modeladmin library
jimport('frenchconnections.models.payment');

/**
 * HelloWorldList Model
 */
class FrenchConnectionsModelRealEstatePayment extends FrenchConnectionsModelPayment
{
  /*
   * Method to process and generate a proforma order...
   *
   *
   */

  public function summary($units = array())
  {
    // Get the vat rate from the item costs config params setting
    $codes = JComponentHelper::getParams('com_realestate');

    // Item costs line holder
    $item_costs = array();

    $item_costs[$codes->get('basic-package')]['quantity'] = 1;

    // Return an array of item costs due for this property
    return $item_costs;
  }

}

