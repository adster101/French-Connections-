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

  /**
   * Override save method so we can retrieve the property ID before saving
   */
  public function save($data)
  {
    if (!$data['unit_id'])
    {
      return false;
    }

    if (empty($data['property_id']))
    {
      $property = $this->getPropertyId($data['unit_id']);
      $data['property_id'] = $property->property_id;
    }

    return parent::save($data);
  }

  /**
   * Method to get some basic unit details for use in the confirmation email
   *  
   * @param int $id
   * @return Object on success, false on failure.
   */
  private function getPropertyId($unit_id = '')
  {
    $query = $this->_db->getQuery(true);
    $query->select('property_id');
    $query->from($this->_db->quoteName('#__unit', 'a'));
    $query->where('a.id = ' . (int) $unit_id);
    $this->_db->setQuery($query);

    try
    {
      $row = $this->_db->loadObject();
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());
      return false;
    }

    return $row;
  }

}