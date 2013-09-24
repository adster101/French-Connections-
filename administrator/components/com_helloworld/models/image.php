<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelImage extends JModelAdmin {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit', 'com_helloworld.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
  public function getTable($type = 'Image', $prefix = 'HelloWorldTable', $config = array()) {
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
    $form = $this->loadForm('com_helloworld.imageupload', 'imageupload', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /**
   * Method to adjust the ordering of a row. Amended to check edit state access check
   *
   * Returns NULL if the user did not have edit
   * privileges for any of the selected primary keys.
   *
   * @param   integer  $pks    The ID of the primary key to move.
   * @param   integer  $delta  Increment, usually +1 or -1
   *
   * @return  mixed  False on failure or error, true on success, null if the $pk is empty (no items selected).
   *
   * @since   12.2
   */
  public function reorder($pks, $delta = 0) {
    $table = $this->getTable();
    $pks = (array) $pks;
    $result = true;

    $allowed = true;

    foreach ($pks as $i => $pk) {
      $table->reset();

      if ($table->load($pk) && $this->checkout($pk)) {
        
        $where = $this->getReorderConditions($table);

        if (!$table->move($delta, $where)) {
          $this->setError($table->getError());
          unset($pks[$i]);
          $result = false;
        }

        $this->checkin($pk);
      } else {
        $this->setError($table->getError());
        unset($pks[$i]);
        $result = false;
      }
    }

    if ($allowed === false && empty($pks)) {
      $result = null;
    }

    // Clear the component's cache
    if ($result == true) {
      $this->cleanCache();
    }

    return $result;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.helloworld.data', array());
    if (empty($data)) {
      $data = $this->getItem();
    }
    return $data;
  }

  /**
   * A protected method to get a set of ordering conditions.
   *
   * @param   object	A record object.
   *
   * @return  array  An array of conditions to add to add to ordering queries.
   * @since   1.6
   */
  protected function getReorderConditions($table) {
    $condition = array();
    $condition[] = 'property_id = ' . (int) $table->property_id;
    return $condition;
  }

}
