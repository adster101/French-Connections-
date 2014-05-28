<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class FeaturedPropertiesModelFeaturedProperty extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'FeaturedProperty', $prefix = 'FeaturedPropertiesTable', $config = array()) {

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
    $form = $this->loadForm('com_featuredproperties.featuredproperty', 'featuredproperty', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
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
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_featuredproperties.edit.featuredproperty.data', array());

    if (empty($data)) {
      $data = $this->getItem();
      if (!empty($data->start_date) && !empty($data->end_date)) {
        $data->start_date = JFactory::getDate($data->start_date)->calendar('d-m-Y');
        $data->end_date = JFactory::getDate($data->end_date)->calendar('d-m-Y');
      }
    }



    return $data;
  }

  public function save($data) {

    // Convert the dates to the correct format at set them to 1430 rather than midnight
    $data['start_date'] = JFactory::getDate($data['start_date'] . '14:30:00')->toSql();
    $data['end_date'] = JFactory::getDate($data['end_date'] . '14:30:00')->toSql();

    return parent::save($data);
  }

}