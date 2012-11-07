<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
/**
 * HelloWorld Model
 */
class HelloWorldModelFacilities extends JModelAdmin
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
	public function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_helloworld.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getAttributesTable($type = 'Attributes', $prefix = 'HelloWorldTable', $config = array()) 
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
		$form = $this->loadForm('com_helloworld.facilities', 'facilities', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_helloworld.edit.facilities.data', array());
		
    if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
  
	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @param	string	The name of the plugin group to import (defaults to "content").
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data)
	{
    if (!empty($data)) {
      // Generate the XML to inject into the form
      $XmlStr = $this->getAttributesXml($form, $data);
      $form->load($XmlStr);
    } 
	}  
  /**
   * getAttributesXml - This function takes a form and some data to generate a set of XML form field definitions. These 
   * definitions are then injected into the form so they are displayed on the facilities admin screen.
   * 
   * @param type $form
   * @param type $data
   * @return string 
   */
  protected function getAttributesXml ($form, $data = array()) 
  {
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form>';
    $counter=0;
    
    $XmlStr.= '
    <fieldset name="internal_facilities" description="COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES">
      <field
        checked="321" 
        class="checkbox inline" 
        name="internal_facilities" 
        type="facilities" 
        label="COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES_LABEL" 
        description="COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES_DESC" 
        multiple="true"
        id="9">
			</field>
      <field type="spacer" name="internal_facilities_spacer" hr="true" />
      <field
        name="internal_facilities_other"
        type="editor"
        label="COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES_OTHER_LABEL"
        description="COM_HELLOWORLD_ACCOMMODATION_INTERNAL_FACILITIES_OTHER_DESC"
        filter="JComponentHelper::filterText"
        buttons="false"
        height="125px">
      </field>
    </fieldset>
    <fieldset name="external_facilities" description="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES">
      <field
        checked="321" 
        class="checkbox inline" 
        name="external_facilities" 
        type="facilities" 
        label="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES_LABEL" 
        description="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES_DESC" 
        multiple="true"
        id="10">
      </field>
      <field type="spacer" name="external_facilities_spacer" hr="true" />
      <field
        name="external_facilities_other"
        type="editor"
        placeholder="false"      
        label="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES_OTHER_LABEL"
        description="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES_OTHER_DESC"
        filter="JComponentHelper::filterText"
        buttons="false"
        height="125px">
      </field>
    </fieldset>
    <fieldset name="kitchen_facilities" description="COM_HELLOWORLD_ACCOMMODATION_KITCHEN_FACILITIES">
      <field
        checked="321" 
        class="checkbox inline" 
        name="kitchen_facilities" 
        type="facilities" 
        label="COM_HELLOWORLD_ACCOMMODATION_KITCHEN_FACILITIES_LABEL" 
        description="COM_HELLOWORLD_ACCOMMODATION_KITCHEN_FACILITIES_DESC" 
        multiple="true"
        id="11">
      </field>
    </fieldset>   
    <fieldset name="activities" description="COM_HELLOWORLD_ACCOMMODATION_ACTIVITIES">
      <field
        checked="321" 
        class="checkbox inline" 
        name="activities" 
        type="facilities" 
        label="COM_HELLOWORLD_ACCOMMODATION_ACTIVITIES_LABEL" 
        description="COM_HELLOWORLD_ACCOMMODATION_EXTERNAL_FACILITIES_DESC" 
        multiple="true"
        id="8">
      </field>
      <field type="spacer" name="activities_spacer" hr="true" />
      <field
        name="activities_other"
        type="editor"
        placeholder="false"      
        label="COM_HELLOWORLD_ACCOMMODATION_ACTIVITIES_OTHER_LABEL"
        description="COM_HELLOWORLD_ACCOMMODATION_ACTIVITIES_OTHER_DESC"
        filter="JComponentHelper::filterText"
        buttons="false"
        height="125px">
      </field>
    </fieldset>   
    <fieldset name="suitability" description="COM_HELLOWORLD_ACCOMMODATION_SUITABILITY">
      <field
        checked="321" 
        class="checkbox inline" 
        name="suitability" 
        type="facilities" 
        label="COM_HELLOWORLD_ACCOMMODATION_SUITABILITY_LABEL" 
        description="COM_HELLOWORLD_ACCOMMODATION_SUITABILITY_DESC" 
        multiple="true"
        id="12">
      </field>
    </fieldset>';
    
    $XmlStr.='</form>';
    return $XmlStr;    
  }
    

  
  public function getItem($pk = null) {
    
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
    
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
    
		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
    
    // Get an instance of the attributes table - Possibly need to merge this into com_attributes
    $attributesTable = $this->getAttributesTable();
    
    $facilities = $attributesTable->loadFacilities();
    
    

    // Need to select all attributes that we are interested in 
    
    
    // Now we need to get the existing attribute details for this property
    //if ($pk > 0)
    //{
      
      //$facilities = $facilitiesTable->loadPropertyAttributes($pk);
      // Check for a table object error.
      //if ($facilities === false && $table->getError())
      //{
        //$this->setError($facilitiesTable->getError());
        //return false;
      //}
    //}    
    
    $properties['attributes'] = $facilities;

    
    $item = JArrayHelper::toObject($properties, 'JObject');
    
    
    
    return $item;

      }
    /**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])b
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($caption, $name)
	{
		$name = JFile::makeSafe($name);
		return array(
      'attribute_type_id' => $caption,
		);
	}  
      
  
	/**
	 * Method to test whether a user can edit the published state of a property.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   11.1
	 */
	protected function canEditState()
	{
    $comtask = JRequest::getVar('task', '', 'POST', 'string' );
    
    $task = explode('.',$comtask);
    
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
