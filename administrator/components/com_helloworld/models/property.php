<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorldList Model
 */
class HelloWorldModelProperty extends JModelAdmin {
  /*
   * A listing property to store the listing against
   *
   */

  public $listing_id = '';

  /*
   * The owner ID for the user that is renewing...taken from the listing not the session scope
   */
  public $owner_id = '';

  /*
   * Whether this is a renewal or not - determined via the expiry date
   */
  public $renewal = '';

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Property', $prefix = 'HelloWorldTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.property', 'property', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /*
   * Returns a list of units for a given listing id (using the units model)
   *
   * @param int The id of the parent property listing
   * return array An array of units along with image counts...
   *
   */

  public function getRenewalSummary() {

    // The call to getState also calls populateState (if it hasn't been called already)
    $id = $this->getState($this->getName() . '.id', '');

    if (empty($id)) {
      // No ID
      return false;
    }

    // Get an instance of the units model
    $model = JModelLegacy::getInstance('listing', 'HelloWorldModel');

    $listing = $model->getItems();

    if (!$listing) {
      return false;
    }

    // Get the user details based on the owner of the property
    $this->owner_id = ($listing[0]->created_by) ? $listing[0]->created_by : '';

    // Set the renewal status
    $this->renewal = (!empty($listing[0]->expiry_date)) ? true : false;

    // Get the user details
    $user = $this->getUser($this->owner_id);

    // Get the order summary, consists of item codes and quantities
    $order_summary = $this->summary($listing);

    // Get the item cost details based on the summary
    $item_costs = $this->getItemCosts($order_summary);

    // Add the VAT status to the order summary
    $vat_status = ($user->vat_status) ? $user->vat_status : 'Z';

    // Calculate the value of each line of the order...
    $order_summary = $this->getOrderTotals($order_summary, $item_costs, $vat_status);

    // Get vouchers, need to pick up any vouchers that are added against a property here
    // Detect the inclusion into the French site network


    $order_summary = JArrayHelper::toObject($order_summary);



    return $order_summary;
  }

  /*
   * Calculate the line costs for the pro forma order
   *
   */

  public function getOrderTotals($order_summary = array(), $item_costs = array(), $vat_status) {

    // Get the vat rate from the item costs config params setting
    $vat = JComponentHelper::getParams('com_itemcosts')->get('vat');

    // Loop over each order line and merge the item cost in
    foreach ($order_summary as $order => &$line) {
      if (array_key_exists($order, $item_costs)) {

        $line = array_merge($order_summary[$order], $item_costs[$order]);
      }

      if ($vat_status == 'S2' || $vat_status == 'S2A') {
        $line['vat'] = $line['quantity'] * $line['cost'] * $vat;
      } else {
        $line['vat'] = 0;
      }
      $line['line_value'] = $line['quantity'] * $line['cost'];

    }

    return $order_summary;
  }

  /*
   * Get the item costs for the renewal
   *
   */

  public function getItemCosts($order_summary = array()) {

    if (empty($order_summary)) {
      return array();
    }

    $items = array_keys($order_summary);

    // Required objects
    $db = JFactory::getDbo();

    foreach ($items as $key => &$item) {
      $item = $db->quote($item);
    }

    $item_codes = implode(',', $items);

    $query = $db->getQuery(true);
    $query->select('code, description as item_description, cost, catid');
    $query->from('#__item_costs');

    $query->where('code in (' . $item_codes . ')');


    $db->setQuery($query);

    $result = $db->loadAssocList($key = 'code');


    return $result;
  }

  /*
   * Method to get the payment form bound with the details of the
   * user renewing the property...
   *
   */

  public function getUserFormDetails() {

    // Get a copy of the form we are using to collect the user invoice address and vat status
    $form = $this->loadForm('com_helloworld.helloworld', 'ordersummary', array('control' => 'jform', 'load_data' => false));

    if (empty($form)) {
      return false;
    }

    $item = $this->getUser($this->owner_id);

    $form->bind($item);

    return $form;
  }

  /*
   * Method to get the user details...
   *
   */

  protected function getUser($user_id = '') {

    // Now get the user details
    $table = $this->getTable('UserProfileFc', 'HelloWorldTable');

    // Attempt to load the row.
    $return = $table->load($user_id);

    // Check for a table object error.
    if ($return === false && $table->getError()) {
      $this->setError($table->getError());
      return false;
    }

    $properties = $table->getProperties(1);

    $item = JArrayHelper::toObject($properties, 'JObject');
    // We need to do the following so we can group the vat fields into a JForm XML field definition
    // Group vat fields
    $vat = array();
    $vat['vat_status'] = $item->vat_status;
    $vat['company_number'] = $item->company_number;
    $vat['vat_number'] = $item->vat_number;

    $item->vat = $vat;

    // Group invoice address fields...
    $invoice_address = array();
    $invoice_address['address1'] = $item->address1;
    $invoice_address['address2'] = $item->address2;
    $invoice_address['city'] = $item->city;
    $invoice_address['region'] = $item->region;
    $invoice_address['postal_code'] = $item->postal_code;
    $invoice_address['country'] = $item->country;

    $item->invoice_address = $invoice_address;

    return $item;
  }

  /*
   * Method to process and generate a proforma order...
   *
   *
   */

  protected function summary($units = array()) {

    // $units contains the listing including all the units and so on.
    // From this we can generate our pro forma order
    // Need to know the user invoice address and VAT status for this user
    // If we don't know the VAT status then intially apply it.
    // If the property is a B&B, then only charge for one unit, regardless of how many units are listed.
    // If the expiry date is set then this is a renewal. Regardless of whether it has actually expired or not
    // Item codes will be dependent on whether it is a renewal or not
    // Flag to indicate whether we've alrady counted a b&B unit

    $bed_and_breakfast = false;

    // A unit counter
    $unit_count = 0;

    // Total images holder
    $image_count = 0;

    // Item costs line holder
    $item_costs = array();

    // Loop over all the units found
    foreach ($units as $unit) {

      if ($unit->accommodation_type == 25) {

        $unit_count++;
      } else {

        (!$bed_and_breakfast) ? $unit_count++ : '';

        $bed_and_breakfast = true;
      }

      // If image count less than number of images on this unit, update image count
      ($image_count < $unit->images) ? $image_count = $unit->images : '';
    }
    // Below covers most cases
    // Need to also consider
    // Site network, e.g. French Translations
    // Video
    // Booking form, although not counted at renewal
    // Link to personal site
    // Special offers - figure out how to deal with

    if ($this->renewal) {

      // Determine the item costs
      if ($image_count >= 8) { // Renewal
        $item_costs['1004-009']['quantity'] = 1;
        $item_costs['1004-006']['quantity'] = $unit_count;
      } else { // Image count must be less than 8 but still a renewal
        $item_costs['1004-002']['quantity'] = 1;
        $item_costs['1004-006']['quantity'] = $unit_count;

        if ($image_count > 4 && $image_count <= 7) {

          // Additional images
          $additional_images = $image_count - 4;

          $item_costs['1004-005']['quantity'] = $additional_images;
        }
      }
    } else { // New property being published for first time
      // Determine the item costs
      if ($image_count >= 8) { // Renewal
        $item_costs['1004-009']['quantity'] = 1;
        $item_costs['1004-006']['quantity'] = $unit_count;
      } else { // Image count must be less than 8
        $item_costs['1005-002']['quantity'] = 1;
        $item_costs['1005-006']['quantity'] = $unit_count;

        if ($image_count > 4 && $image_count <= 7) {

          // Additional images
          $additional_images = $image_count - 4;

          $item_costs['1005-005']['quantity'] = $additional_images;
        }
      }
    }

    return $item_costs;
  }

}
