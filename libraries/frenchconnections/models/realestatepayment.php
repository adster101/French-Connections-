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

  /**
   * Method to update a property record given a listing ID
   * 
   * @param type $listing_id - The property listing ID to update
   * @param type $cost - The cost to the owner of this update
   * @param type $review - The resulting review status
   * @param type $expiry_date - The new expiry date
   * @param type $published - The new published state
   * @param type $autorenewal - The autorenewal transaction ID
   * @return boolean
   */
  public function updateProperty($listing_id = '', $cost = '', $review = 1, $expiry_date = '', $published = '', $autorenewal = '')
  {

    // Initialise some variable
    $data = array();
    $data['id'] = $listing_id;

    /*
     * Set the review status, default to 1 if non supplied...
     */
    $data['review'] = $review;

    /*
     * Update the cost of this latest update
     */

    $data['value'] = $cost;

    /*
     * Update the autorenwal transaction id
     */
    $data['VendorTxCode'] = $autorenewal;

    /*
     * Update the expiry date if one is passed in
     */
    if (!empty($expiry_date))
    {
      $data['expiry_date'] = $expiry_date;
    }

    /*
     * Also update the published, if requested...
     */
    if (!empty($published))
    {
      $data['published'] = $published;
    }

    $table = JTable::getInstance('Property', 'RealestateTable');


    // Store the data.
    if (!$table->save($data))
    {
      $this->setError($table->getError());
      return false;
    }

    return true;
  }

}

