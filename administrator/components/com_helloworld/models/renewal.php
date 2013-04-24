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
   * Returns a list of units for a given listing id (using the units model)
   * 
   * @param int The id of the parent property listing
   * return array An array of units along with image counts...
   *    
   */
  public function getUnits() {
    
  

    // Get an instance of the units model
    $model = JModelLegacy::getInstance('Units','HelloWorldModel');
    
    
    $units = $model->getItems();
    
    return $units;
    
    
    
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
	 * Method to get a single record.
	 *
	 * @param   integer	The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
      	
      // We need to do the following so we can group the vat fields into a JForm XML field definition
      // Group vat fields
      
      $vat = array();
      $vat['vat_status'] = $item->vat_status;
      $vat['company_number'] = $item->vat_status;
      $vat['vat_number'] = $item->vat_status;
      
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
      
		}
    
		return $item;
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
  
  /*
   * Preprocess the form so we can set the property ID for which to assign this offer to
   * 
   * 
   */
  public function preprocessForm(JForm $form, $data) {
    
    // Get the user ID 
    $user = JFactory::getUser()->id;
    
    // Get the property id 
    $id = JRequest::getVar('id','','GET','int'); 

    if (!empty($data->vat_status))
    {
      
      // This user already has VAT status setup so we know it...
			$form->removeGroup('vat'); 
      
    }
    
    if (!empty($data->address1) && !empty($data->city) && !empty($data->postal_code))
    {
      $form->removeGroup('invoice_address');
    }
  }
  
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_helloworld/models/forms/offers.js';
	}
  
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
    // Load the listing details from the session (stored in the controller)
    
    $listing = JApplication::getUserState($this->extension . '.listing.detail', '');

    $user_id = ($listing->created_by) ? $listing->created_by : '';
    
    // Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_helloworld.property.renewal.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem($user_id);
		}
    
 		return $data;
	}	
}
