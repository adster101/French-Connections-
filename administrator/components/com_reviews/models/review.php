<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class ReviewsModelReview extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit');
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
	public function getTable($type = 'Review', $prefix = 'ReviewTable', $config = array()) 
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
		$form = $this->loadForm('com_reviews.reviews', 'review', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
    
		return $form;
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
		$data = JFactory::getApplication()->getUserState('com_reviews.edit.review.data', array());
    
    if (empty($data)) 
		{
			$data = $this->getItem();
		}   

    return $data;
	}
  

  
  /* Method to preprocess the special offer edit form */

  protected function preprocessForm(JForm $form, $data)
  {

    // Get the user
    $user = JFactory::getUser();

    // If the user is authorised to edit state then we assume they have general admin rights
    if ($user->authorise('core.edit.state', 'com_reviews'))
    {

      $form->setFieldAttribute('unit_id', 'type', 'unit');

      if (!empty($data->property_id))
      {
        $form->setFieldAttribute('unit_id', 'readonly', 'true');
      }
    }
    else
    {
      // Remove these fields for non authed user groups.
      $form->removeField('published');
      $form->removeField('approved_by');
      $form->removeField('approved_date');
      $form->removeField('id');
    }
  }
  
  
}