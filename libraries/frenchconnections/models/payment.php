<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modeladmin library
jimport('joomla.application.component.modellegacy');

/**
 * HelloWorldList Model
 */
class FrenchConnectionsModelPayment extends JModelLegacy
{

  /**
   * Internal memory based cache array of data.
   *
   * @var    array
   * @since  12.2
   */
  protected $cache = array();

  /**
   * The property listing as a list of units 
   * 
   * @var type 
   */
  protected $listing = array();

  /*
   * A listing property to store the listing against
   *
   */
  protected $listing_id = '';

  /*
   * The owner ID for the user that is renewing...taken from the listing not the session scope
   */
  public $owner_id = '';

  /*
   * Whether this is a renewal or not - determined via the expiry date
   */
  protected $isRenewal = '';

  /*
   * The expiry date of the listing being edited
   */
  protected $expiry_date = '';

  /*
   * The review status of the property
   */
  protected $isReview = '';

  /*
   * The property type payment is being calculated for.
   */
  protected $property_type = '';

  /**
   * __construct - initialise the various class properties that we need to process a listing
   * @param type $config
   */
  function __construct($config = array())
  {

    parent::__construct($config);

    if (array_key_exists('listing', $config))
    {

      // Set the model properties here.
      $this->listing = $config['listing'];
      $this->owner_id = ($config['listing'][0]->created_by) ? $config['listing'][0]->created_by : '';
      /*
       * Determine whether this is a renewal. 
       */
      $renewal = $config['renewal'];
      $this->setIsRenewal($renewal);

      $this->listing_id = $config['listing'][0]->id;
      $this->expiry_date = $config['listing'][0]->expiry_date;
      $this->isReview = $config['listing'][0]->review;
    }
  }

  public function clearVouchers($listing_id)
  {
    // Get the db object
    $db = JFactory::getDBO();
    // Get a date so we can expire all vouchers listed against a property
    $date = JFactory::getDate('-1 day')->calendar('Y-m-d');
    $yesterday = JFactory::getDate('')->calendar('Y-m-d');


    $query = $db->getQuery(true);

    $query->update('#__vouchers');
    $query->set($db->quoteName('end_date') . ' = ' . $db->quote($yesterday));
    $query->set($db->quoteName('date_redeemed') . ' = ' . $db->quote($date));
    $query->where($db->quoteName('property_id') . ' = ' . (int) $listing_id);

    try
    {
      $db->setQuery($query);
      $db->execute();
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   * 
   */
  public function getTable($type = 'Property', $prefix = 'RentalTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Loops through the 'current' version and checks whether any of the lines were present in the previous version
   * If so, determine the quantity they had on previous version and subtract that from the current version.
   *  
   * 
   * @param type $current_order_summary
   * @param type $previous_order_summary
   * @return type
   */
  public function getPaymentDue($current_order_summary = array(), $previous_order_summary = array())
  {
    $order_lines_due = array();
    $quantity_to_charge = '';

    foreach ($current_order_summary as $item => $quantity)
    {
      // The previous version had some of this already (e.g. units) possibly images.
      if (array_key_exists($item, $previous_order_summary))
      {
        // The previous version had some of this already (e.g. units) possibly images.        
        $quantity_to_charge = $quantity['quantity'] - $previous_order_summary[$item]['quantity'];

        if ($quantity_to_charge > 0)
        {
          $order_lines_due[$item]['quantity'] = $quantity_to_charge;
        }
      }
      else
      {
        // The previous version didn't have any of these, so bill 'em all
        $order_lines_due[$item]['quantity'] = $quantity['quantity'];
      }
    }

    return $order_lines_due;
  }

  /*
   * Returns a list of order lines for a listing based on what combination of units/images and so on the listing has
   *
   * @param int The id of the parent property listing
   * return array An array of units along with image counts...
   *
   */

  public function getPaymentSummary($current = array(), $previous = array(), $component = 'com_rental')
  {

    $order_summary = array();

    // Get the user details
    $user = $this->getUser($this->owner_id);

    $order_summary = $this->summary($current, $component);

    // If we have a previous version and this is not a renewal 
    if (!empty($previous) && !$this->getIsRenewal())
    {
      $previous_order_summary = $this->summary($previous);

      // Determine whether any additional payment is due.
      $order_summary = $this->getPaymentDue($order_summary, $previous_order_summary);
    }

    // Get any vouchers applied to this property
    $vouchers = $this->getVouchers($this->listing_id);

    $order = array_merge($order_summary, $vouchers);

    if (empty($order))
    {
      return false;
    }

    // Get the item cost details based on the summary
    $item_costs = $this->getItemCosts($order);

    // Add the VAT status to the order summary
    $vat_status = ($user->vat_status) ? $user->vat_status : 'S20';

    // Calculate the value of each line of the order...
    $summary_tmp = $this->getOrderLineTotals($order, $item_costs, $vat_status);

    // Get vouchers, need to pick up any vouchers that are added against a property here
    // Detect the inclusion into the French site network

    $summary = JArrayHelper::toObject($summary_tmp);

    return $summary;
  }

  /**
   * Calculate the line costs for the pro forma order
   * 
   * @param array $order_summary
   * @param array $item_costs
   * @param string $vat_status
   * @return array 
   */
  public function getOrderLineTotals($order_summary = array(), $item_costs = array(), $vat_status)
  {

    // Get the vat rate from the item costs config params setting
    $vat = JComponentHelper::getParams('com_itemcosts')->get('vat');

    // Get any discount vouchers being applied to this order
    // Appears as a duplicate call but this retrieves discount vouchers, 
    // which apply to the total order line, including any other vouchers!
    $vouchers = $this->getVouchers($this->listing_id, true);

    // Loop over each order line and merge the item cost in
    foreach ($order_summary as $order => &$line)
    {
      // This bit check to see if there is a note field in the order line
      // If so, it would've come from a (non-discount) voucher containing a note...
      if (array_key_exists('note', $line))
      {
        $description = $item_costs[$order]["item_description"] . ' - ' . $order_summary[$order]['note'];
        $item_costs[$order]["item_description"] = $description;
      }

      // Add the cost and detail of each item code to the order line
      if (array_key_exists($order, $item_costs))
      {
        $line = array_merge($order_summary[$order], $item_costs[$order]);
      }

      // Add the vat status etc 
      if ($vat_status == 'S20' || $vat_status == 'S2A')
      {
        $line['vat'] = $line['quantity'] * $line['cost'] * $vat;
      }
      else
      {
        $line['vat'] = 0;
      }
      $line['line_value'] = $line['quantity'] * $line['cost'];
    }

    // Need to apply any discount vouchers
    // 1. For each line calculate the discount
    // 2. Total this up
    // 3. Add a discount line to the order
    if (!empty($vouchers))
    {

      $order_total = '';
      $vat_total = '';

      foreach ($order_summary as $k => $v)
      {
        // Calculate the discounts based on the order 
        $order_total = $order_total + $v['line_value'];
        $vat_total = $vat_total + $v['vat'];
      }

      $discount = array();
      $discount['quantity'] = 1;
      $discount['code'] = $vouchers[0]->item_cost_id;
      // Append any notes to the discount voucher
      $discount['item_description'] = $vouchers[0]->description . ' - ' . $vouchers[0]->note;
      $discount['cost'] = ($order_total * $vouchers[0]->cost);
      $discount['vat'] = ($vat_total * $vouchers[0]->cost);
      $discount['line_value'] = ($order_total * $vouchers[0]->cost);

      $order_summary[$vouchers[0]->item_cost_id] = $discount;
    }

    return $order_summary;
  }

  /**
   * getOrderTotal - returns the total price for the payment
   */
  public function getOrderTotal($order = array())
  {

    $order_total = '';

    /*
     * Get the order total
     */
    foreach ($order as $line => $line_detail)
    {
      $order_total = $order_total + $line_detail->line_value + $line_detail->vat;
    }


    return $order_total;
  }

  /*
   * Get the item costs for the renewal
   *
   */

  public function getItemCosts($order_summary = array())
  {

    if (empty($order_summary))
    {
      return array();
    }

    $items = array_keys($order_summary);

    // Required objects
    $db = JFactory::getDbo();

    foreach ($items as $key => &$item)
    {
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

  public function getUserFormDetails()
  {

    // Get a copy of the form we are using to collect the user invoice address and vat status
    $form = $this->loadForm('com_rental.helloworld', 'ordersummary', array('control' => 'jform', 'load_data' => false));

    if (empty($form))
    {
      return false;
    }

    $item = $this->getUser($this->owner_id);

    $form->bind($item);

    return $form;
  }

  /*
   * Method to get the payment form
   *
   */

  public function getPaymentFormDepreciated()
  {

    $form = $this->loadForm('com_rental.helloworld', 'payment', array('control' => 'jform', 'load_data' => false));

    if (empty($form))
    {
      return false;
    }

    $data = JFactory::getApplication()->getUserState('com_rental.renewal.data', array());
    $data['id'] = $id = $this->getState($this->getName() . '.id', '');

    $form->bind($data);

    return $form;
  }

  /*
   * Method to get the user details...
   *
   */

  public function getUser($user_id = '')
  {

    // Use the cached data if possible.
    if ($this->retrieve($user_id))
    {
      return $this->retrieve($user_id);
    }

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    // Now get the user details
    $table = $this->getTable('UserProfileFc', 'RentalTable');

    // Attempt to load the row.
    $return = $table->load($user_id);

    // Check for a table object error.
    if ($return === false && $table->getError())
    {
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

    $this->store($user_id, $item);

    return $this->retrieve($user_id);
  }

  /*
   * Method to process and generate a proforma order...
   *
   *
   */

  public function summary($units = array(), $component = 'com_rental')
  {
    // Get the vat rate from the item costs config params setting
    $codes = JComponentHelper::getParams($component);

    // $units contains the listing including all the units and so on.
    // From this we can generate our pro forma order
    // Need to know the user invoice address and VAT status for this user
    // If the property is a B&B, then don't charge for any additional units unless the additional units are self-catering
    // If the expiry date is set then this is a renewal. Regardless of whether it has actually expired or not   
    // Item codes are dependent on whether it is a renewal or not
    // Units counters
    $bandb = 0;
    $selfcatering = 0;
    $mixed_units = false;

    $unit_count = '';

    // Total images holder
    $image_count = 0;

    // Item costs line holder
    $item_costs = array();

    // Loop over all the units found
    foreach ($units as $unit)
    {

      // Don't charge for and add unpublished units
      if ($unit->published == 0)
      {
        continue;
      }

      if ($unit->accommodation_type == 25)
      {

        $selfcatering++;
      }
      elseif ($unit->accommodation_type == 24)
      {

        $bandb++;
      }

      // Images are done as a total. So 5 images on each unit with 3 units would give 15 images. And so on.
      $image_count = $image_count + $unit->images;
    }

    // Work out the unit count
    if ($bandb > 0 && $selfcatering > 0)
    {
      $unit_count = $selfcatering; // Set total units to the number of self-catering
      $mixed_units = true; // Flag the property as having mixed units...
    }
    elseif ($bandb == 0 && $selfcatering > 0)
    {
      $unit_count = ( $selfcatering - 1 ); // Remove one as the first is included in the package price
    }
    elseif ($bandb > 0 && $selfcatering == 0)
    {
      $unit_count = 0; // Don't charge for additional units as B&B have unlimited
    }

    // If this is a straightforward renewal then calculate everything...based on renewal prices
    if ($this->getIsRenewal())
    {
      // Determine the item costs
      if ($image_count >= 8)
      {
        //$item_costs['1004-009']['quantity'] = 1; // Renewal
        //$item_costs['1002-008']['quantity'] = 1; // Renewal
        $item_costs[$codes->get('professional-package-renewal')]['quantity'] = 1; // Renewal
      }
      else
      {
        // Image count must be less than 8 
        //$item_costs['1002-004']['quantity'] = 1;
        $item_costs[$codes->get('basic-package-renewal')]['quantity'] = 1;
      }

      // Add any additional units not included in base price 
      if ($unit_count > 0)
      {
        //$item_costs['1002-010']['quantity'] = $unit_count;
        $item_costs[$codes->get('additional-unit-renewal')]['quantity'] = $unit_count;
      }
    }
    // New property being published for first time...
    elseif (!$this->getIsRenewal() && empty($this->expiry_date))
    {
      // Determine the item costs
      if ($image_count >= 8)
      {
        //$item_costs['1005-009']['quantity'] = 1;
        //$item_costs['1003-008']['quantity'] = 1;
        $item_costs[$codes->get('professional-package')]['quantity'] = 1;
      }
      else
      {
        // Image count must be less than 8
        //$item_costs['1003-004']['quantity'] = 1;
        $item_costs[$codes->get('basic-package')]['quantity'] = 1;
      }
      // Add any additional units not included in base price 
      if ($unit_count > 0)
      {
        //$item_costs['1003-010']['quantity'] = $unit_count;
        $item_costs[$codes->get('additional-unit')]['quantity'] = $unit_count;
      }
    }
    // Not a renewal and not a new property...just calculate any additional units etc
    elseif (!$this->getIsRenewal() && !empty($this->expiry_date))
    {
      // Add any additional units not included in base price 
      if ($unit_count > 0)
      {
        $item_costs[$codes->get('additional-unit')]['quantity'] = $unit_count;
      }

      // If there are more than eight images in total they should be 'upgraded' to the unlimited package
      if ($image_count >= 8)
      {
        $item_costs[$codes->get('unlimited-upgrade')]['quantity'] = 1;
      }
    }

    // If this property version has B&B and S/C then add it as an item cost
    if ($mixed_units && !$this->getIsRenewal())
    {
      $item_costs['1005-014']['quantity'] = 1;
    }

    // If renewal
    if ($this->getIsRenewal())
    {
      // Do we have a LWL listing?
      if (!empty($unit->lwl))
      {
        $item_costs[$codes->get('lwl-renewal')]['quantity'] = 1;
      }
      // Do we have a video listing
      if (!empty($unit->video_url))
      {
        $item_costs[$codes->get('video-renewal')]['quantity'] = 1;
      }
      // Do we have a translation listing
      if (!empty($unit->frtranslation))
      {
        $item_costs[$codes->get('frtranslation-renewal')]['quantity'] = 1;
      }
    }
    //If not renewal
    elseif (!$this->getIsRenewal())
    {
      // Do we have a LWL listing?
      if (!empty($unit->lwl))
      {
        $item_costs[$codes->get('lwl')]['quantity'] = 1;
      }
      // Do we have a video listing
      if (!empty($unit->video_url))
      {
        $item_costs[$codes->get('video')]['quantity'] = 1;
      }
      // Do we have a translation listing
      if (!empty($unit->frtranslation))
      {
        $item_costs[$codes->get('frtranslation')]['quantity'] = 1;
      }
    }


    // TO DO - Lastly, need to account for an existing package switching between basic and unlimited images
    return $item_costs;
  }

  public function processRepeatPayment($VendorTxCode = '', $VPSTxId = '', $SecurityKey = '', $TxAuthNo = '', $type = 'REPEAT', $payment_summary = '', $id = '')
  {

    // Check we've got what we need to proceed
    if (!$VendorTxCode || !$VPSTxId || !$SecurityKey || !$TxAuthNo)
    {
      return false;
    }
    // Get the invoice component parameters which hold the protx settings
    $protx_settings = JComponentHelper::getParams('com_itemcosts');

    $sngTotal = 0.0;
    $strProtocol = $protx_settings->get('VPSProtocol');
    $strTransactionType = $type;
    $strVendorName = $protx_settings->get('VendorName');
    $strPurchaseURL = $protx_settings->get('RepeatURL');
    $strCurrency = $protx_settings->get('Currency');
    $VendorTxCodeNew = $this->owner_id . '-' . $id . '-' . date("ymdHis", time()) . rand(0, 32000) * rand(0, 32000);

    // Loop over the order lines and make the basket - wrap into separate function
    foreach ($payment_summary as $item => $line)
    {
      $sngTotal = $sngTotal + $line->line_value + $line->vat;
    }

    /* Now to build the Sage Pay Direct POST.  For more details see the Sage Pay Direct Protocol 2.23
     * * NB: Fields potentially containing non ASCII characters are URLEncoded when included in the POST */
    $strPost = "VPSProtocol=" . $strProtocol;
    $strPost = $strPost . "&TxType=" . $strTransactionType; //PAYMENT by default.  You can change this in the includes file
    $strPost = $strPost . "&Vendor=" . $strVendorName;
    $strPost = $strPost . "&VendorTxCode=" . $VendorTxCodeNew; //As generated above
    $strPost = $strPost . "&Amount=" . number_format($sngTotal, 2); //Formatted to 2 decimal places with leading digit but no commas or currency symbols **
    $strPost = $strPost . "&Currency=" . $strCurrency;
    $strPost = $strPost . "&Description=Repeat-Deferred";
    $strPost = $strPost . "&RelatedVPSTxID=" . $VPSTxId;
    $strPost = $strPost . "&RelatedVendorTxCode=" . $VendorTxCode;
    $strPost = $strPost . "&RelatedSecurityKey=" . $SecurityKey;
    $strPost = $strPost . "&RelatedTxAuthNo=" . $TxAuthNo;

    // Update the data array with a few more bits and pieces
    $data['Amount'] = $sngTotal;
    $data['VendorTxCode'] = $VendorTxCodeNew;
    $data['user_id'] = $this->owner_id;
    $data['property_id'] = $id;
    $data['TxType'] = $type;
    $data['DateCreated'] = JFactory::getDate()->toSql();
    $data['id'] = '';
    
    // Store the transaction in the protx payment page
    if (!$this->saveProtxTransaction($data))
    {
      // Error is set in the function
      return false;
    }

    $this->saveProtxTransactionLines($payment_summary, $VendorTxCodeNew);

    $arrResponse = $this->requestPost($strPurchaseURL, $strPost);
    /* Analyse the response from Sage Pay Direct to check that everything is okay
     * * Registration results come back in the Status and StatusDetail fields */
    $arrResponse['VendorTxCode'] = $VendorTxCodeNew;

    $strStatus = $arrResponse["Status"];

    if ($strStatus == "OK")
    {
      // Update the protx transactio line
      $this->saveProtxTransaction($arrResponse);

      return $VendorTxCodeNew;
    }
    else
    {
      return false;
    }
  }

  public function cancelRepeatPayment($VendorTxCodeNew, $VPSTxId = '', $SecurityKey = '', $TxAuthNo = '', $type = 'ABORT')
  {

    // Check we've got what we need to proceed
    if (!$VendorTxCodeNew || !$VPSTxId || !$SecurityKey || !$TxAuthNo)
    {
      return false;
    }
    // Get the invoice component parameters which hold the protx settings
    $protx_settings = JComponentHelper::getParams('com_itemcosts');

    $strProtocol = $protx_settings->get('VPSProtocol');
    $strTransactionType = $type;
    $strVendorName = $protx_settings->get('VendorName');
    $strPurchaseURL = $protx_settings->get('AbortURL');

    /* Now to build the Sage Pay Direct POST.  For more details see the Sage Pay Direct Protocol 2.23
     * * NB: Fields potentially containing non ASCII characters are URLEncoded when included in the POST */
    $strPost = "VPSProtocol=" . $strProtocol;
    $strPost = $strPost . "&TxType=" . $strTransactionType; //ABORT by default.  
    $strPost = $strPost . "&Vendor=" . $strVendorName;
    $strPost = $strPost . "&VendorTxCode=" . $VendorTxCodeNew; //As generated above
    $strPost = $strPost . "&VPSTxId=" . $VPSTxId;
    $strPost = $strPost . "&SecurityKey=" . $SecurityKey;
    $strPost = $strPost . "&TxAuthNo=" . $TxAuthNo;
    $arrResponse = $this->requestPost($strPurchaseURL, $strPost);
    /* Analyse the response from Sage Pay Direct to check that everything is okay
     * * Registration results come back in the Status and StatusDetail fields */
    $arrResponse['VendorTxCode'] = $VendorTxCode;
    $strStatus = $arrResponse["Status"];

    if ($strStatus == "OK")
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
   * 
   */

  public function processPayment($data, $current_version = array(), $previous_version = array(), $component = 'com_rental')
  {

    // Determine whether this payment should be saved as an auto-renewal or not
    $shouldAutoRenew = (!empty($data['autorenewal'])) ? $data['autorenewal'] : '0';

    // Transaction id for repeat payment, if above is true
    $transaction_id = '';

    // Get the order summary details
    $order = $this->getPaymentSummary($current_version, $previous_version, $component);

    // Get the invoice component parameters which hold the protx settings
    $protx_settings = JComponentHelper::getParams('com_itemcosts');

    $sngTotal = 0.0;
    $strBasket = "";
    $iBasketItems = 0;
    $VendorTxCode = '';

    $strProtocol = $protx_settings->get('VPSProtocol');
    $strTransactionType = $protx_settings->get('TransactionType');
    $strVendorName = $protx_settings->get('VendorName');
    $strCurrency = $protx_settings->get('Currency');
    $strPurchaseURL = $protx_settings->get('PurchaseURL');

    // Okay, so we have validated the form and we have the order summary
    // First off, generate a VendorTxCode and stash what we have in the db
    $VendorTxCode = $this->owner_id . '-' . $data['id'] . '-' . date("ymdHis", time()) . '-' . rand(0, 32000) * rand(0, 32000);

    $description = 'Payment received for PRN[' . $data['id'] . '] ' . 'OWNER[' . $this->owner_id . ']';

    // Loop over the order lines and make the basket - wrap into separate function
    foreach ($order as $item => $line)
    {
      $iBasketItems = $iBasketItems + 1;
      $strBasket = $strBasket . ':' . 'PRN[' . $data['id'] . '] ' . 'OWNER[' . $this->owner_id . '] ' . '[' . $line->code . '] [' . $line->item_description . ']:' . $line->quantity;
      $strBasket = $strBasket . ":" . number_format($line->cost, 2);/** Price ex-Vat * */
      $strBasket = $strBasket . ":" . number_format($line->vat, 2);/** VAT component * */
      $strBasket = $strBasket . ":" . number_format($line->cost, 2);/** Item price * */
      $strBasket = $strBasket . ":" . number_format($line->line_value + $line->vat, 2);/** Line total * */
      $sngTotal = $sngTotal + $line->line_value + $line->vat;
    }

    // Update the data array with a few more bits and pieces
    $data['Amount'] = $sngTotal;
    $data['VendorTxCode'] = $VendorTxCode;
    $data['user_id'] = $this->owner_id;
    $data['TxType'] = $strTransactionType;
    $data['property_id'] = $data['id'];
    $data['DateCreated'] = JFactory::getDate()->toSql();
    $data['id'] = '';
    $data['CardLastFourDigits'] = substr($data['CardNumber'], -4, 4);

    // Add the total number of items to the basket string
    $strBasket = $iBasketItems . $strBasket;

    if (!$this->saveProtxTransaction($data))
    {
      // Error is set in the function
      return false;
    }

    $this->saveProtxTransactionLines($order, $VendorTxCode);

    /* Now to build the Sage Pay Direct POST.  For more details see the Sage Pay Direct Protocol 2.23
     * * NB: Fields potentially containing non ASCII characters are URLEncoded when included in the POST */
    $strPost = "VPSProtocol=" . $strProtocol;
    $strPost = $strPost . "&TxType=" . $strTransactionType; //PAYMENT by default.  You can change this in the includes file
    $strPost = $strPost . "&Vendor=" . $strVendorName;
    $strPost = $strPost . "&VendorTxCode=" . $VendorTxCode; //As generated above
    $strPost = $strPost . "&Amount=" . number_format($sngTotal, 2); //Formatted to 2 decimal places with leading digit but no commas or currency symbols **
    $strPost = $strPost . "&Currency=" . $strCurrency;
    $strPost = $strPost . "&CardType=" . $data['CardType'];

    $strPost = $strPost . "&CardHolder=" . $data['CardHolder'];
    $strPost = $strPost . "&CardNumber=" . $data['CardNumber'];
    if (strlen($data['CardStartDate']) > 0)
      $strPost = $strPost . "&StartDate=" . $data['CardStartDate'];
    $strPost = $strPost . "&ExpiryDate=" . $data['CardExpiryDate'];
    if (strlen($data['IssueNumber']) > 0)
      $strPost = $strPost . "&IssueNumber=" . $data['IssueNumber'];
    $strPost = $strPost . "&CV2=" . $data['CV2'];

    // Send the IP address of the person entering the card details
    $strPost = $strPost . "&ClientIPAddress=127.0.0.1";
    /* Allow fine control over 3D-Secure checks and rules by changing this value. 0 is Default **
     * * It can be changed dynamically, per transaction, if you wish.  See the Sage Pay Direct Protocol document */
    $strPost = $strPost . "&Apply3DSecure=0";

    /** It can be changed dynamically, per transaction, if you wish.  See the Sage Pay Direct Protocol document */
    if ($strTransactionType !== "AUTHENTICATE")
      $strPost = $strPost . "&ApplyAVSCV2=0";
    // Add the basket
    $strPost = $strPost . "&Basket=" . urlencode($strBasket); //As created above
    $strPost = $strPost . "&Description=" . urlencode($description); //As created above

    /* Billing Details
     * This section is optional in its entirety but if one field of the address is provided then all non-optional fields must be provided
     * If AVS/CV2 is ON for your account, or, if paypal cardtype is specified and its not via PayPal Express then this section is compulsory */
    $strPost = $strPost . "&BillingFirstnames=" . urlencode($data["BillingFirstnames"]);
    $strPost = $strPost . "&BillingSurname=" . urlencode($data["BillingSurname"]);
    $strPost = $strPost . "&BillingAddress1=" . urlencode($data["BillingAddress1"]);
    if (strlen($data["BillingAddress2"]) > 0)
      $strPost = $strPost . "&BillingAddress2=" . urlencode($data["BillingAddress2"]);
    $strPost = $strPost . "&BillingCity=" . urlencode($data["BillingCity"]);
    $strPost = $strPost . "&BillingPostCode=" . urlencode($data["BillingPostCode"]);
    $strPost = $strPost . "&BillingCountry=" . urlencode($data["BillingCountry"]);

    /* Delivery Details
     * * This section is optional in its entirety but if one field of the address is provided then all non-optional fields must be provided
     * * If paypal cardtype is specified then this section is compulsory */
    $strPost = $strPost . "&DeliveryFirstnames=" . urlencode($data["BillingFirstnames"]);
    $strPost = $strPost . "&DeliverySurname=" . urlencode($data["BillingSurname"]);
    $strPost = $strPost . "&DeliveryAddress1=" . urlencode($data["BillingAddress1"]);
    $strPost = $strPost . "&DeliveryCity=" . urlencode($data["BillingCity"]);
    $strPost = $strPost . "&DeliveryPostCode=" . urlencode($data["BillingPostCode"]);
    $strPost = $strPost . "&DeliveryCountry=" . urlencode($data["BillingCountry"]);

    /* Send the account type to be used for this transaction.  Web sites should us E for e-commerce **
     * * If you are developing back-office applications for Mail Order/Telephone order, use M **
     * * If your back office application is a subscription system with recurring transactions, use C **
     * * Your Sage Pay account MUST be set up for the account type you choose.  If in doubt, use E * */
    $strPost = $strPost . "&AccountType=E";

    $arrResponse = $this->requestPost($strPurchaseURL, $strPost);
    /*
     * Analyse the response from Sage Pay Direct to check that everything is okay
     * Registration results come back in the Status and StatusDetail fields
     */
    $arrResponse['VendorTxCode'] = $VendorTxCode;

    $strStatus = $arrResponse["Status"];
    $strStatusDetail = $arrResponse["StatusDetail"];
    // Card details and address details have been checked. Can now process accordingly...
    // Update the database and redirect the user appropriately
    if ($strStatus == "OK")
      $strDBStatus = "AUTHORISED - The transaction was successfully authorised with the bank.";
    elseif ($strStatus == "MALFORMED")
      $strDBStatus = "MALFORMED - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail, 0, 255));
    elseif ($strStatus == "INVALID")
      $strDBStatus = "INVALID - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail, 0, 255));
    elseif ($strStatus == "NOTAUTHED")
      $strDBStatus = "DECLINED - The transaction was not authorised by the bank.";
    elseif ($strStatus == "REJECTED")
      $strDBStatus = "REJECTED - The billing address much match the address at which the card is registered. Please ensure you're using the correct billing address and try again.";
    elseif ($strStatus == "AUTHENTICATED")
      $strDBStatus = "AUTHENTICATED - The transaction was successfully 3D-Secure Authenticated and can now be Authorised.";
    elseif ($strStatus == "REGISTERED")
      $strDBStatus = "REGISTERED - The transaction was could not be 3D-Secure Authenticated, but has been registered to be Authorised.";
    elseif ($strStatus == "ERROR")
      $strDBStatus = "ERROR - There was an error during the payment process.  The error details are: " . mysql_real_escape_string($strStatusDetail);
    else
      $strDBStatus = "UNKNOWN - An unknown status was returned from Sage Pay.  The Status was: " . mysql_real_escape_string($strStatus) . ", with StatusDetail:" . mysql_real_escape_string($strStatusDetail);

    // Save the transaction out to the protx table, this effectively updates the row with the 
    // response from Protx
    $transaction = $this->saveProtxTransaction($arrResponse, 'VendorTxCode');

    if ($shouldAutoRenew)
    {
      $transaction_id = $transaction->id;
    }

    // Okay now we have processed the transaction and update it in the db.
    switch ($strStatus) {
      case 'OK':
        //$this->setMessage("AUTHORISED - The transaction was successfully authorised with the bank.");
        $return = array('order' => $order, 'payment' => $arrResponse, 'autorenew' => $transaction_id);
        return $return;
        break;
      case 'MALFORMED':
        $this->setError("MALFORMED - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail, 0, 255)));
        return false;
        break;

      case 'INVALID':
        $this->setError("INVALID - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail, 0, 255)));
        return false;
        break;

      case 'NOTAUTHED':
        $this->setError("DECLINED - The transaction was not authorised by the bank.");
        return false;
        break;

      case 'DECLINED':
        $this->setError("DECLINED - The transaction was not authorised by the bank.");
        return false;
        break;

      case 'REJECTED':
        $this->setError("REJECTED - The transaction was failed by your 3D-Secure or AVS/CV2 rule-bases.");
        return false;
        break;

      case 'ERROR':
        $this->setError("ERROR - There was an error during the payment process.  The error details are: " . mysql_real_escape_string($strStatusDetail));
        return false;
        break;

      default:
        $this->setError("UNKNOWN - An unknown status was returned from Sage Pay.  The Status was: " . mysql_real_escape_string($strStatus) . ", with StatusDetail:" . mysql_real_escape_string($strStatusDetail));
        return false;
        break;
    }
  }

  /**
   * This method determines whether we have just processed a renewal or a new sign up payment
   * and does the relevant processing on the property listing.
   * 
   * @param type $order_payment_details
   * @param type $billing_details
   * @return boolean
   */
  public function processListing($order_payment_details = array(), $billing_details = array())
  {

    /*
     * Set up variables used in the listing processing
     * 
     */
    $order = $order_payment_details['order'];
    $payment_details = $order_payment_details['payment'];
    $transaction_id = $order_payment_details['autorenew'];
    $listing_id = ($this->getListingId()) ? $this->getListingId() : '';
    $expiry_date = ($this->getExpiryDate()) ? $this->getExpiryDate() : '';
    $params = JComponentHelper::getParams('com_rental');
    $date = JFactory::getDate();
    $from = array($params->get('payment_admin_email', 'accounts@frenchconnections.co.uk'), $params->get('payment_admin_name', 'French Connections Accounts'));

    $billing_name = $billing_details['BillingFirstnames'] . ' ' . $billing_details['BillingSurname'];
    $transaction_number = $payment_details['VendorTxCode'];
    $auth_code = $payment_details['TxAuthNo'];
    $address = $billing_details['BillingAddress1'] . ' ' . $billing_details['BillingAddress2'] . ' ' . $billing_details['BillingCity'] . ' ' . $billing_details['BillingPostCode'] . ' ' . $billing_details['BillingCountry'];
    $billing_email = (JDEBUG) ? 'accounts@frenchconnections.co.uk' : $billing_details['BillingEmailAddress'];

    $html = false;
    $description = "\n";

    foreach ($order as $orderline)
    {
      $description .= '[' . $orderline->code . ']' . $orderline->item_description . "\n";
    }

    if ($this->getIsRenewal() && !$this->getIsReview())
    {

      /*
       * Get the payment total that has just been processed
       */
      $total = $this->getOrderTotal($order);

      // Straightforward renewal 
      // Update the expiry date
      $expiry_date = $this->getNewExpiryDate();

      if (!$this->updateProperty($listing_id, $total, 0, $expiry_date, $published = 1, $autorenewal = $transaction_id, true))
      {
        // TO DO - Log this
        return false;
      }

      // Send payment receipt
      $receipt_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_SUBJECT', $billing_name, $total, $listing_id);
      $receipt_body = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_BODY', $date, $billing_name, $total, $transaction_number, $auth_code, $description, $address, $billing_email);
      $this->sendEmail($from, $billing_email, $receipt_subject, $receipt_body, $html);

      // Send the renewal confirmation email           
      $confirmation_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_SUBJECT', $listing_id);
      $confirmation_body = JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_BODY', $billing_name);
      $this->sendEmail($from, $billing_email, $confirmation_subject, $confirmation_body, $html);

      $message = JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_NO_CHANGES');

      return $message;
    }
    else if ($this->getIsRenewal() && $this->getIsReview())
    {
      // Get the amound paid
      $total = $this->getOrderTotal($order);

      // Generate a new expiry date based on today
      $expiry_date = $this->getNewExpiryDate();

      // Renewal with amendments, update the total and review state.
      $this->updateProperty($listing_id, $total, $review = 2, $expiry_date, '', '', $autorenewal = $transaction_id, true);

      // Send payment receipt
      $receipt_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_SUBJECT', $billing_name, $total, $listing_id);
      $receipt_body = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_BODY', $date, $billing_name, $total, $transaction_number, $auth_code, $description, $address, $billing_email);
      $this->sendEmail($from, $billing_email, $receipt_subject, $receipt_body, $html);

      // Send the renewal confirmation email           
      $confirmation_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_SUBJECT', $listing_id);
      $confirmation_body = JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_BODY', $billing_name);
      $this->sendEmail($from, $billing_email, $confirmation_subject, $confirmation_body, $html);

      $message = JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_CONFIRMATION_WITH_CHANGES');

      return $message;
    }
    else if (empty($expiry_date) && !$this->getIsRenewal())
    {

      // New property
      $total = $this->getOrderTotal($order);

      // Update the review status 
      $this->updateProperty($listing_id, $total, $review = 2, '', '', $autorenewal = $transaction_id, true);

      // Send payment receipt
      $receipt_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_SUBJECT', $billing_name, $total, $listing_id);
      $receipt_body = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_BODY', $date, $billing_name, $total, $transaction_number, $auth_code, $description, $address, $billing_email);
      $this->sendEmail($from, $billing_email, $receipt_subject, $receipt_body, $html);

      $message = JText::_('COM_RENTAL_HELLOWORLD_NEW_PROPERTY_CONFIRMATION');

      return $message;
      // Send confirmation of submission
    }
    else if (!empty($expiry_date) && !$this->getIsRenewal() && $this->getIsReview())
    {

      // Existing property that has been updated. - May not be appropriate here...or this may 
      // only be called when they need to pay extra
      // Update review status
      // If payment made - send payment receipt
      $total = $this->getOrderTotal($order);

      if (round($total, 2) > 0)
      {
        // Send payment receipt
        $receipt_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_SUBJECT', $billing_name, $total, $listing_id);
        $receipt_body = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_BODY', $date, $billing_name, $total, $transaction_number, $auth_code, $description, $address, $billing_email);
        $this->sendEmail($from, $billing_email, $receipt_subject, $receipt_body, $html);
      }

      $this->updateProperty($listing_id, $total, $review = 2, '', '', $autorenewal = $transaction_id);

      $message = JText::_('COM_RENTAL_PROPERTY_EXISTING_PROPERTY_UPDATE_WITH_PAYMENT');

      return $message;
    }
    else if (!empty($expiry_date) && !$this->getIsRenewal() && !$this->getIsReview())
    {
      // Must be processing a voucher or some other gubbins
      $total = $this->getOrderTotal($order);

      if (round($total, 2) > 0)
      {
        // Send payment receipt
        $receipt_subject = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_SUBJECT', $billing_name, $total, $listing_id);
        $receipt_body = JText::sprintf('COM_RENTAL_HELLOWORLD_PAYMENT_RECEIPT_BODY', $date, $billing_name, $total, $transaction_number, $auth_code, $description, $address, $billing_email);
        $this->sendEmail($from, $billing_email, $receipt_subject, $receipt_body, $html);
      }

      $this->clearVouchers($listing_id);

      $message = JText::_('COM_RENTAL_PROPERTY_ADDITIONAL_PAYMENT_RECEIVED');

      return $message;
    }
  }

  /**
   * 
   */
  public function sendEmail($from = array(), $to = '', $emailSubject = '', $emailBody = '', $html = true)
  {

    // Assemble the email data...
    $mail = JFactory::getMailer()
            ->setSender($from)
            ->addRecipient($to)
            ->setSubject($emailSubject)
            ->setBody($emailBody)
            ->isHtml($html);

    // If debug is off then we should have a $cc, at least for the renewals.
    $mail->addBCC('accounts@frenchconnections.co.uk');
    $mail->addBCC('adamrifat@frenchconnections.co.uk');

    if (!$mail->Send())
    {
      return false;
    }

    return true;
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
   * @param type $reset_snooze - boolean whether to reset the snooze date or not.
   * @return boolean
   */
  public function updateProperty($listing_id = '', $cost = '', $review = 1, $expiry_date = '', $published = '', $autorenewal = '', $reset_snooze = false)
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
    if (!empty($autorenewal))
    {
      $data['VendorTxCode'] = $autorenewal;
    }
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
    
    if ($reset_snooze)
    {
      $data['snooze_until'] = '';
    }

    $table = JTable::getInstance('Property', 'RentalTable');

    // Store the data.
    if (!$table->save($data))
    {
      $this->setError($table->getError());
      return false;
    }

    return true;
  }

  /*   * ***********************************************************
    Send a post request with cURL
    $url = URL to send request to
    $data = POST data to send (in URL encoded Key=value pairs)
   * *********************************************************** */

  public function requestPost($url, $data)
  {
    // Set a one-minute timeout for this script
    set_time_limit(60);

    // Initialise output variable
    $output = array();

    // Open the cURL session
    $curlSession = curl_init();

    // Set the URL
    curl_setopt($curlSession, CURLOPT_URL, $url);
    // No headers, please
    curl_setopt($curlSession, CURLOPT_HEADER, 0);
    // It's a POST request
    curl_setopt($curlSession, CURLOPT_POST, 1);
    // Set the fields for the POST
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
    // Return it direct, don't print it out
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
    // This connection will timeout in 30 seconds
    curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
    //The next two lines must be present for the kit to work with newer version of cURL
    //You should remove them if you have any problems in earlier versions of cURL
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

    //Send the request and store the result in an array

    $rawresponse = curl_exec($curlSession);
    //Store the raw response for later as it's useful to see for integration and understanding
    $_SESSION["rawresponse"] = $rawresponse;
    //Split response into name=value pairs
    $response = explode("\n", $rawresponse);
    // Check that a connection was made
    if (curl_error($curlSession))
    {
      // If it wasn't...
      $output['Status'] = "FAIL";
      $output['StatusDetail'] = curl_error($curlSession);
    }

    // Close the cURL session
    curl_close($curlSession);

    // Tokenise the response
    for ($i = 0; $i < count($response); $i++)
    {
      // Find position of first "=" character
      $splitAt = strpos($response[$i], "=");
      // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
      $output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt + 1)));
    } // END for ($i=0; $i<count($response); $i++)
    // Return the output
    return $output;
  }

// END function requestPost()

  /**
   * Function to save a transaction out to the protx transaction table
   * 
   * @param type $data
   * @param type $key - Determines whether to update an existing row based on key passed or insert a new row
   * @return boolean
   */
  public function saveProtxTransaction($data = array(), $key = '')
  {

    if (array_key_exists('CardExpiryDate', $data))
    {
      // Process the expiry date into last day of given month year
      $year = '20' . substr($data['CardExpiryDate'], -2);
      $month = substr($data['CardExpiryDate'], 0, 2);
      $expiry_date = new DateTime($year . '-' . $month . '-01');
      $data['CardExpiryDate'] = $expiry_date->format('Y-m-t');
    }

    // So let's put the transaction into the database
    $table = JTable::getInstance('protxtransactions', 'RentalTable');

    if (!empty($key))
    {
      $table->set('_tbl_keys', array('VendorTxCode'));
      $table->load($data['VendorTxCode']);
    }

    // Bind the data.
    if (!$table->bind($data))
    {
      $this->setError($table->getError());
      return false;
    }

    // Store the data.
    if (!$table->store())
    {
      $this->setError($table->getError());
      return false;
    }

    return $table;
  }

  public function saveProtxTransactionLines($data = array(), $VendorTxCode = '')
  {
    // So let's put the transaction lines into the database
    $table = JTable::getInstance('protxtransactionlines', 'RentalTable');

    // Add each of the order lines to the transaction lines table
    foreach ($data as $line)
    {
      $line->VendorTxCode = $VendorTxCode;
      $line->id = '';
      // Bind the data.
      if (!$table->bind($line))
      {
        $this->setError($table->getError());
        return false;
      }

      // Store the data.
      if (!$table->store())
      {
        $this->setError($table->getError());
        return false;
      }
    }

    return true;
  }

  /**
   * Generate a new expiry date for the property based on todays date.
   * 
   * @param type $period
   * @return type
   */
  public function getNewExpiryDate($period = 'P365D')
  {
    $days_to_expiry = PropertyHelper::getDaysToExpiry($this->getExpiryDate());

    $expiry_date = $this->getExpiryDate();

    if (empty($expiry_date) || $days_to_expiry <= 0)
    {
      /**
       * Get the date now
       */
      $date = JFactory::getDate();
    }
    elseif ($days_to_expiry > 0)
    {
      $date = JFactory::getDate($this->getExpiryDate());
    }

    /*
     * Add the date period to it
     */
    $date->add(new DateInterval($period));

    /*
     * Format the new date
     */
    $new_expiry_date = $date->toSql();

    return $new_expiry_date;
  }

  /**
   *  * 
   * Gets a list of vouchers applied to any particular property
   * 
   * @param type $property_id
   * @return mixed
   * 
   */
  public function getVouchers($property_id = '', $discount = false)
  {

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $date = JFactory::getDate()->calendar('Y-m-d');
    $vouchers = array();
    $query->select('a.item_cost_id, a.quantity, a.date_redeemed, b.cost, b.description, a.note');
    $query->from('#__vouchers a');
    $query->where('property_id = ' . (int) $property_id);
    $query->where('end_date >= ' . $db->quote($date));
    $query->join('left', '#__item_costs b on b.code = a.item_cost_id');

    // Don't return the discount vouchers 
    if (!$discount)
    {
      $query->where('b.catid not in (50)');
    }
    else
    {
      $query->where('b.catid in (50)');
    }

    $query->where('a.state = 1');

    $db->setQuery($query);

    try
    {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      // Problem loading vouchers for this property
      return false;
    }

    // Discount vouchers are ignored and only applied once
    if (!$discount && count($rows) > 0)
    {
      foreach ($rows as $row)
      {
        $vouchers[$row->item_cost_id]['quantity'] = $row->quantity;
        $vouchers[$row->item_cost_id]['note'] = $row->note;
      }

      return $vouchers;
    }

    return $rows;
  }

  /**
   * Get the renewal status
   * @return boolean
   */
  public function getIsRenewal()
  {
    // Set the renewal status...
    return $this->isRenewal;
  }

  /**
   * Method to determine the renewal status based on the expiry date
   * 
   * @return void
   */
  protected function setIsRenewal($renewal = false)
  {
    $this->isRenewal = $renewal;
  }

  /**
   * Get the expiry date
   * @return string
   */
  protected function getExpiryDate()
  {
    return $this->expiry_date;
  }

  /**
   * 
   * @return type
   */
  protected function getIsReview()
  {
    return $this->isReview;
  }

  /**
   * Get the listing ID
   * @return int
   */
  protected function getListingId()
  {
    return $this->listing_id;
  }

  /**
   * Get the listing detail
   * 
   * @return array
   */
  protected function getListing()
  {
    return $this->listing;
  }

  /**
   * Get the user id of the user who owns this property
   * @return int
   */
  protected function getOwnerId()
  {
    return $this->owner_id;
  }

  /**
   * Method to retrieve data from cache.
   *
   * @param   string   $id          The cache store id.
   * @param   boolean  $persistent  Flag to enable the use of external cache. [optional]
   *
   * @return  mixed  The cached data if found, null otherwise.
   *
   * @since   2.5
   *
   */
  public function retrieve($id)
  {

    $data = null;

    // Use the internal cache if possible.
    if (isset($this->cache[$id]))
    {
      return $this->cache[$id];
    }

    // Store the data in internal cache.
    if ($data)
    {
      $this->cache[$id] = $data;
    }

    return $data;
  }

  /**
   * Method to store data in cache.
   *
   * @param   string   $id          The cache store id.
   * @param   mixed    $data        The data to cache.
   *
   * @return  boolean  True on success, false on failure.
   *
   * @since   2.5
   */
  protected function store($id, $data)
  {

    // Store the data in internal cache.
    $this->cache[$id] = $data;

    return true;
  }

}
