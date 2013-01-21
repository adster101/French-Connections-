<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelHelloWorld extends JModelAdmin {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  public function allowEdit($data = array(), $key = 'id') {
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
  public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array()) {
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
    $form = $this->loadForm('com_helloworld.helloworld', 'helloworld', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /*
   * Method to get a form for the user to choose which property they would like to add a unit to
   * 
   */

  public function getNewPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.userproperties', 'userproperties', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
  }

  /**
   * Method to get the script that have to be included on the form
   *
   * @return string	Script files
   */
  public function getScript() {
    return 'administrator/components/com_helloworld/models/forms/helloworld.js';
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

  /*
   * This method checks whether the property being edited is a unit.
   * If it is then we take the lat and long from the parent property 
   * and force those to be the same for this property.
   * 
   * This can happen from two places.
   * Firstly, if a user is adding a new property they may choose a parent property
   * in which case we take the parent_id from the user session.
   * 
   * Secondly, if the user is editing an existing property which already has a 
   * parent_id set. I.e. is already marked as a unit. In this case it will be set
   * in the $data scope.
   * 
   * param JForm $form The JForm instance for the view being edited
   * param array $data The form data as derived from the view (may be empty)
   * 
   * @return void
   * 
   */

  protected function preprocessForm(JForm $form, $data) {

    // More robustly checked on the component level permissions?
    // E.g. at the moment any user who is not owner can edit this? 
    // e.g. add a new permission core.edit.property.changeparent
    
    $isOwner = HelloWorldHelper::isOwner();

    // Scope parent_id from the user session scope
    $parent_id = JApplication::getUserState('parent_id');

    // If $data->parent_id is set and it's not null or 1 (e.g. a unit)
    if (isset($data->parent_id) && $data->parent_id != 1 && $data->parent_id != '') {
     
      // Use getItem to get the data for the parent property if supplied
      $parent_prop = $this->getItem($data->parent_id);

      // Set the location details accordingly (why bother here, just unset it all?)
      $data->latitude = $parent_prop->latitude;
      $data->longitude = $parent_prop->longitude;
      $data->city = $parent_prop->city;
      $data->department = $parent_prop->department;
      $data->distance_to_coast = $parent_prop->distance_to_coast;

      foreach ($form->getFieldSet('Location') as $field) {
        // So we loop over the fields disabling them and making them non-required in the form
        // This ensure that they will not be editable by the user in this instance. 
        $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'readonly', 'true');

      }
      
      // Lastly add the city field via an XML string
      $XmlStr = '';
      $XmlStr .= '<form>';
      $XmlStr .= $this->getNearestCityXml($form, $data, true);

 
      // Check is this is owner, if not owner then add the userproperties field.
      if (!$isOwner) {
        $XmlStr .= $this->getUserPropertiesXml($form, $data);
      }
      
      $XmlStr .= '</form>';
      $form->load($XmlStr);    
    } else if (!isset($data->parent_id) && $parent_id !='') { 
      // Seems to apply when the user is creating a new property as parent_id 
      // is set in the session scope in the sub controller
      // Otherwise if parent id not set in $data but has been taken from session scope
      // e.g. new unit being added      
      // Use getItem to get the data for the parent property if supplied
      $parent_prop = $this->getItem($parent_id);

      // Set the location details accordingly
      $data->latitude = $parent_prop->latitude;
      $data->longitude = $parent_prop->longitude;
      $data->city = $parent_prop->city;
      $data->department = $parent_prop->department;
      $data->distance_to_coast = $parent_prop->distance_to_coast;


      if ($parent_id != 1) {
        foreach ($form->getFieldSet('Location') as $field) {
          // So we loop over the fields disabling them and making them non-required in the form
          // This ensure that they will not be editable by the user in this instance. 
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'readonly', 'true');
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'class', 'readonly');
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'required', 'false');
        }
      }

      // Set the parent_id value in $data
      $data->set('parent_id', $parent_id);

      // Lastly add the city field via an XML string
      $XmlStr = '';
      $XmlStr .= '<form>';
      $XmlStr .= $this->getNearestCityXml($form, $data, true);
      $XmlStr .= '</form>';
      $form->load($XmlStr);
      
    } else if (!empty($data)) {  
      
      // Else, if owner AND $data is not empty (e.g. an existing property)
      // Lastly add the city field via an XML string
      $XmlStr = '';
      $XmlStr .= '<form>';
      $XmlStr .= $this->getNearestCityXml($form, $data, false);
      $XmlStr .= '</form>';
      $form->load($XmlStr);
    } 



    // Reset the user state as otherwise parent_id in session scope will interfere
    // with normal editing etc
    JApplication::setUserState('parent_id', '');
  }
  
  /**
   * Method to return the neatest city xml foeld definition string
   *
   * @param   object    $form, the form instance
   * @param   mixed     $data, the form data 
   * @param   boolean   is this a property owner? 
   * 
   * @return  string .
   *
   * @since   11.1
   */
  protected function getUserPropertiesXml($form, $data) {
    $XmlStr = '';
    
    $XmlStr .= '<field
			name="parent_id"
			type="UserProperties"
			label="COM_CATEGORIES_FIELD_PARENT_LABEL"
			description="COM_CATEGORIES_FIELD_PARENT_DESC"
			class="validate-parent input-small"
      labelclass="control-label"
			required="true"></field>';  
    
    return $XmlStr;
  }
  
  /**
   * Method to return the neatest city xml foeld definition string
   *
   * @param   object    $form, the form instance
   * @param   mixed     $data, the form data 
   * @param   boolean   is this a property owner? 
   * 
   * @return  string .
   *
   * @since   11.1
   */
  protected function getNearestCityXml($form, $data, $readonly = 'true') {
    $latitude = (!empty($data->latitude) ? $data->latitude : 0);
    $longitude = (!empty($data->longitude) ? $data->longitude : 0);   
    $readonly = ($readonly) ? 'true' : 'false';
    
    $XmlStr = '<field
      name="city"
      type="cities"
      extension="com_helloworld"
      class="inputbox validate-nearesttown"
      labelclass="control-label"
      label="COM_HELLOWORLD_HELLOWORLD_FIELD_NEARESTTOWN_LABEL"
      description="COM_HELLOWORLD_HELLOWORLD_FIELD_NEARESTTOWN_DESC"
      required="true"
      readonly="' . $readonly . '"
      filter="JComponentHelper::filterText"
      maxlength="75"
      validate="nearesttown"
      latitude="' . $latitude . '"
      longitude="' . $longitude . '">
      <option value="">COM_HELLOWORLD_HELLOWORLD_FIELD_SELECT_NEAREST_TOWN</option>

    </field>';
    
    return $XmlStr;
  }

  /**
   * Method to test whether a record can be deleted.
   *
   * @param   object  $record  A record object.
   *
   * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
   *
   * @since   11.1
   */
  protected function canEditState() {
    $comtask = JRequest::getVar('task', '', 'POST', 'string');

    $task = explode('.', $comtask);

    $user = JFactory::getUser();

    if ($task[1] == 'orderdown' || $task[1] == 'orderup') {
      return $user->authorise('helloworld.edit.reorder', $this->option);
    } else if ($task[1] == 'publish' || $task[1] == 'unpublish') {
      return $user->authorise('helloworld.edit.publish', $this->option);
    } else if ($task[1] == 'trash') {
      return $user->authorise('helloworld.edit.trash', $this->option);
    } else {
      return false;
    }
  }

}
