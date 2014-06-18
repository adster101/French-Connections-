<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class RentalModelOffer extends JModelAdmin
{

  
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Offer', $prefix = 'RentalTable', $config = array()) 
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
		$form = $this->loadForm('com_rental.helloworld', 'offer', array('control' => 'jform', 'load_data' => $loadData));
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
    
    // and set the property data values accordingly...
    $data->property_id = $id;
    $data->created_by = $user;
  }
  
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_rental/models/forms/offers.js';
	}
  
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{

    // Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('offer.edit.helloworld', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
}
