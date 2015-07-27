<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class NotesModelNote extends JModelAdmin {

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
    $form = $this->loadForm('com_notes.note', 'note', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
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
  public function getTable($type = 'Note', $prefix = 'NotesTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {

    // Get the application
    $app = JFactory::getApplication();

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_notes.edit.note.data', array());

    if (empty($data)) {

      $data = $this->getItem();

      $property_id = $app->input->get('property_id', 0, 'int');

      if ($property_id != 0) {
        $data->property_id = $property_id;
      }
    }

    return $data;
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return  void
   *
   * @since   2.5
   */
  protected function populateState() {
    parent::populateState();

    $property_id = JFactory::getApplication()->input->get('property_id', 0, 'int');
    $this->setState('note.property_id', $property_id);
  }
}