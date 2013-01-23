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
  
  /*
   * Method to get a form for the admin user to choose which account they would like to add a property to
   * 
   */

  public function getNewAdminPropertyForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.addpropertybyuser', 'addpropertybyuser', array('control' => 'jform', 'load_data' => $loadData));
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
    
    $canDo = $this->getState('actions.permissions',array());
    // If we don't come from a view then this maybe empty so we reset it.
    if (empty($canDo)) {
      $canDo = HelloWorldHelper::getActions();
    }
    
    $isOwner = HelloWorldHelper::isOwner();

    // If $data->parent_id is set and it's not null or 1 (e.g. a unit)
    if (isset($data->parent_id) && $data->parent_id != 1 && !empty($data->parent_id)) {

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
     
      $form->setFieldAttribute('city', 'latitude', $data->latitude );
      $form->setFieldAttribute('city', 'longitude', $data->longitude);

      $form->removeField('map');

      
      
    } else if (!empty($data) && $data->parent_id == 1) {

      // We are editing an existing property here which isn't a child
      $latitude = (!empty($data->latitude) ? $data->latitude : 0);
      $longitude = (!empty($data->longitude) ? $data->longitude : 0);
      
      $form->setFieldAttribute('city', 'latitude', $latitude );
      $form->setFieldAttribute('city', 'longitude', $longitude);

      // Check the parent editing ability of this user
      if ($canDo->get('helloworld.edit.property.parent')) {
        $XmlStr = $this->getUserPropertiesXml($form, $data);
        $form->load('<form>' . $XmlStr . '</form>');
      }
      
      // Check the change parent ability of this user
      if (!$canDo->get('helloworld.edit.property.owner')) {
  			$form->removeField('created_by');
      }
      
    } else if (!isset($data->parent_id) && !isset($data->created_by)) { 

      // Only applies when a user is creating a new property as parent_id is set in the session scope in the sub controller
      // Otherwise if parent id not set in $data but has been taken from session scope e.g. new unit being added      
      // Use getItem to get the data for the parent property if supplied
     
      // Scope parent_id from the user session scope
      $parent_id = JApplication::getUserState('parent_id','');

      $data->parent_id = $parent_id;

      // If parent id = 1 this is a new parent property
      if ($parent_id !=1 && $parent_id !='') { 
        
        // Get the parent details for the property id supplied
        $parent_prop = $this->getItem($parent_id);
      
        $form->setFieldAttribute('city', 'latitude', $parent_prop->latitude );
        $form->setFieldAttribute('city', 'longitude', $parent_prop->longitude);
        
        // Set the location details accordingly
        $data->latitude = $parent_prop->latitude;
        $data->longitude = $parent_prop->longitude;
        $data->city = $parent_prop->city;
        $data->location_type = $parent_prop->location_type;
        $data->department = $parent_prop->department;
        $data->distance_to_coast = $parent_prop->distance_to_coast;
        
        foreach ($form->getFieldSet('Location') as $field) {
          // So we loop over the fields disabling them and making them non-required in the form
          // This ensure that they will not be editable by the user in this instance. 
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'readonly', 'true');
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'class', 'readonly');
          $form->setFieldAttribute(str_replace(array('jform', '[', ']'), '', $field->name), 'required', 'false'); 
        }
        
        $form->removeField('map');

        
      } 
      
      // Check the parent editing ability of this user
      if ($canDo->get('helloworld.edit.property.parent')) {
        
        // Scope created by from the user session scope
        $user = JApplication::getUserState('created_by', '');
        
        $XmlStr = $this->getUserPropertiesXml($form, $data, $user);
        
        $form->load('<form>' . $XmlStr . '</form>');
        $data->created_by = $user;

      }

      // Check the change parent ability of this user
      if (!$canDo->get('helloworld.edit.property.owner')) {
        $form->setFieldAttribute('created_by', 'type', 'hidden');
      }

    } else {
      // Check the parent editing ability of this user
      if ($canDo->get('helloworld.edit.property.parent')) {
        $XmlStr = $this->getUserPropertiesXml($form, $data);
        $form->load('<form>' . $XmlStr . '</form>');
      }      
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
  protected function getUserPropertiesXml($form, $data, $user = '' ) {
    $XmlStr = '';
    
    $XmlStr .= '<field
			name="parent_id"
			type="UserProperties"
			label="COM_CATEGORIES_FIELD_PARENT_LABEL"
			description="COM_CATEGORIES_FIELD_PARENT_DESC"
			class="validate-parent span12"
      labelclass="control-label"
			required="true"
      readonly="true"
      user="' . $user .'"></field>';  

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
      filter="int"
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
    } else if ($task[1] == 'publish' || $task[1] == 'unpublish' || $task[1] == 'trash') {
      return $user->authorise('core.edit.state', $this->option);
    } else {
      return false;
    }
  }
  
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{

		$canDo = HelloWorldHelper::getActions();
		$this->setState('actions.permissions', $canDo);
		
		// List state information.
		parent::populateState();
	}
  
}
