<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class HelloWorldModelRenewal extends JModelAdmin
{

  public $extension = 'com_helloworld';

  /*
   * A units property to store the listing units against
   */
  public $units = '';

  /*
   * A listing property to store the listing against
   *
   */
  public $listing = '';

  /*
   * The owner ID for the user that is renewing...taken from the listing not the session scope
   */
  public $owner_id = '';

  /*
   * The owner profile for the user that is renewing...VAT status and invoice address etc
   */
  public $owner_profile = '';

 /*
   * Returns a list of units for a given listing id (using the units model)
   *
   * @param int The id of the parent property listing
   * return array An array of units along with image counts...
   *
   */

  public function getRenewalSummary() {

    $id = $this->getState($this->getName() . '.id', '');

    if (empty($id)) {
      // No ID
      return false;
    }

    // Get an instance of the units model
    $model = JModelLegacy::getInstance('listing', 'HelloWorldModel');

    $units = $model->getItems();

    if (!$units) {
      return false;
    }

    // $units contains the listing including all the units and so on.
    // From this we can generate our pro forma order
    // Need to know the user invoice address and VAT status for this user
    // If we don't know the VAT status then intially apply it.
    // If the property is a B&B, then only charge for one unit, regardless of how many units are listed.

    $this->owner_id = ($units[0]->created_by) ? $units[0]->created_by : '';

    // Now get the user details
    $table = $this->getTable('UserProfileFc', 'HelloWorldTable');

    //$table->user_id = $this->owner_id;
    // Attempt to load the row.
    $return = $table->load($this->owner_id);

    // Check for a table object error.
    if ($return === false && $table->getError()) {
      $this->setError($table->getError());
      return false;
    }

    //


    return $units;
  }

  /*
   * Returns the property listing given the id (using the property model)...
   *
   * @param int The id of the parent property listing
   *
   * return array An array of units along with image counts...
   *
   */
  public function getListing($id = '') {

    if (empty($id)) {
      // No ID
      return false;
    }

    // Get an instance of the property listing model
    $model = JModelLegacy::getInstance('Property','HelloWorldModel');

    // Get an instance of the units model
    $listing = $model->getItem($id);

    return $listing;

  }

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'UserProfileFc', $prefix = 'HelloWorldTable', $config = array())
	{
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
	public function getForm($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_helloworld.helloworld', 'payment', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}


 public function getUserFormDetails() {

    // Get a copy of the form we are using to collect the user invoice address and vat status
    $form = $this->loadForm('com_helloworld.helloworld', 'payment', array('control' => 'jform', 'load_data' => false));

    if (empty($form)) {
      return false;
    }

    // Now get the user details
    $table = $this->getTable('UserProfileFc', 'HelloWorldTable');

    //$table->user_id = $this->owner_id;
    // Attempt to load the row.
    $return = $table->load($this->owner_id);

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

    $bind = $form->bind($item);

    return $form;
  }

}
