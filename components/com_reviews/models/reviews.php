<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class ReviewsModelReviews extends JModelForm {

  /**
   * @var object item
   */
  protected $item;
  
	/**
	 * Method to get the tetimonial item.
	 *
	 * The base form is loaded from XML and then an event is fired
	 *
	 *
	 * @param	array	$data		An optional array of data for the form to interrogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getItem($data = array(), $loadData = true)
	{
		
		$id = $this->getState('property.id');
    
    // Load the property get method to get the title and what not of the property being testimonialised
    
    
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');
    
    $table = $this->getTable();
 		
    $lang = JFactory::getLanguage()->getDefault();
    
    if ($id > 0)
		{
			// Attempt to load the row. Need to provide the language string here...
			$return = $table->load($id,'', $lang );

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
   
    $properties = $table->getProperties(1);

		$item = JArrayHelper::toObject($properties, 'JObject');

    return $item;
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
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}  
  
	/**
	 * Method to get the contact form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 *
	 *
	 * @param	array	$data		An optional array of data for the form to interrogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = false)
	{
		// Get the form.
		$form = $this->loadForm('com_reviews.review', 'review', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}

		$id = $this->getState('property.id');
    
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
		$data = JFactory::getApplication()->getUserState('com_reviews.review.data', array());
		
    if (empty($data)) 
		{
			$data = $this->getItem();
		}
    
    return $data;
	}	   
  
  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState() {
    
    
    $app = JFactory::getApplication();
    
    $input = $app->input;

    $request = $input->request;

    // Get the message id
    $id = $input->get('id', '', 'int');
    
    $this->setState('property.id', $id);

    parent::populateState();
  }

  

}
