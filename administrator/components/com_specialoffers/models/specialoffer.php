<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class SpecialOffersModelSpecialOffer extends JModelAdmin {

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
  public function getTable($type = 'SpecialOffer', $prefix = 'SpecialOffersTable', $config = array()) {
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
    $form = $this->loadForm('com_specialoffers.specialoffers', 'specialoffer', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
  }

  /* Method to preprocess the special offer edit form */

  protected function preprocessForm(JForm $form, $data) {

    // Get the user
    $user = JFactory::getUser();

    // If the user is authorised to edit state then we assume they have general admin rights
    if ($user->authorise('core.edit.state', 'com_specialoffers')) {

      $field = '<form><fieldset name="publishing"><field name="published" 
          type="list" 
          label="JSTATUS"
          description="JFIELD_PUBLISHED_DESC" 
          class="input-medium"
          filter="intval" 
          size="1" 
          default="0" 
          required="true"
          labelclass="control-label">
      <option value="">JSELECT</option>
			<option value="1">JPUBLISHED</option>
			<option value="0">JUNPUBLISHED</option>
			<option value="2">JARCHIVED</option>
			<option value="-2">JTRASHED</option>
		</field></fieldset></form>';

      $form->setFieldAttribute('unit_id', 'type', 'text');

      if (!empty($data->property_id)) {
        $form->setFieldAttribute('property_id', 'readonly', 'true');
      }

      $form->load($field);
    }
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_specialoffers.edit.specialoffer.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * Method to save the form data.
   *
   * @param	array	The form data.
   *
   * @return	boolean	True on success.
   * @since	1.6
   */
  public function save($data) {
    
    /*
     * Add the helloworld tables to the JTable include path
     */
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables', 'HelloWorldTable');
   
    $app = JFactory::getApplication();
    
    // Get the user
    $user = JFactory::getUser();   
    
    $unit_id = $data['unit_id'];
       
    // Set the date created timestamp
    $data['date_created'] = JFactory::getDate()->toSql();
    
    // Get an instance of the unit table
    $table = $this->getTable('Unit','HelloWorldTable');
    
    // Get the parent property id for the owner of this property 
    if (!$table->load($unit_id)) {
      $this->setError(JText::_('COM_SPECIAL_OFFERS_PROBLEM_CREATING_OFFER'));
      return false;
    }
    
    // Set the user ID in the data array
    $data['created_by'] = $table->created_by;
    $data['property_id'] = $table->property_id;
    
    // TO DO - Add a check that no active offers exist for this unit already
    
    
    if (parent::save($data)) {
     
      // Trigger email to admin user
      
      // Set additional messaging to notify user that offer is awaiting moderation etc.
      if (!$user->authorise('core.edit.state', 'com_specialoffers')) {
        JFactory::getApplication()->enqueueMessage(JText::_('COM_SPECIALOFFERS_OFFER_ADDED_SUCCESS'));
      }
      
      return true;
    }

    return false;
  }


}