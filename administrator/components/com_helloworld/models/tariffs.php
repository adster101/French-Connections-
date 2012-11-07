<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelTariffs extends JModelAdmin
{
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
	public function getTariffsTable($type = 'Tariffs', $prefix = 'HelloWorldTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}  
  
	/**
	 * Method to get the record form. 
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.5
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_helloworld.tariffs', 'tariffs',
		                        array('control' => 'jform', 'load_data' => $loadData));
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
   * 
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_helloworld.edit.helloworld.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
	
  /**
   *
   * Override the getItem method. In this case we need to pull the tariffs into $data object in order to inject 
   * the tariffs into the tariff view.
   * 
   * @param type $pk
   * @return boolean 
   */
  
	public function getItem($pk = null)
	{
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
    
		// Convert to a JObject before adding other data.
		$properties = $table->getProperties(1);
    
    // Now we need to get the existing tariff details for this property
    if ($pk > 0)
    {
      $tariffsTable = $this->getTariffsTable();
      $tariffs = $tariffsTable->load($pk);
      // Check for a table object error.
      if ($tariffs === false && $table->getError())
      {
        $this->setError($tariffs->getError());
        return false;
      }
    }
    
    $properties['tariffs'] = $tariffs;
          
		$item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		return $item;
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
      $XmlStr = $this->getTariffXml($form, $data);
      $form->load($XmlStr);
    }
	}
  
  /**
   * getTariffXml - This function takes a form and some data to generate a set of XML form field definitions. These 
   * definitions are then injected into the form so they are displayed on the tariffs admin screen.
   * 
   * @param type $form
   * @param type $data
   * @return string 
   */
  protected function getTariffXml ($form, $data = array()) 
  {
    
    
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form>';
    $counter=0;
    $XmlStr.='<fields name="tariffs">';

    if (!$data->tariffs) {
      return false;
    }
    
    // Loop over the existing availability first
    foreach ($data->tariffs as $tariff) {
      
      // Ignore the first 'tariff' as it is an error counter added by the load db table instance
      if(count($tariff)) {
        
        $XmlStr.= '
        <fieldset name="tariffs_'.$counter.'">
        <field
          id="start_date_tariff_'.$counter.'"
          name="start_date"
          type="text"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_DESC"
          class="inputbox tariff_date input-small"
          validate=""
          labelclass="tariff-label"
          required="false"
          multiple="true"
          default="'.$tariff->start_date.'"
          readonly="true">
        </field>
        
        <field
          id="end_date_tariff_'.$counter.'"
          name="end_date"
          type="text"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description="COM_HELLOWORLD_TARIFFS_FIELD_DATE_RANGE_DESC"
          class="inputbox tariff_date input-small"
          validate=""
          labelclass="tariff-label"
          required="false"
          multiple="true"
          default="'.$tariff->end_date.'"
          readonly="true">
        </field>       
        
        <field       
          id="tariff_'.$counter.'"
          name="tariff"
          type="text"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_DESC"
          class="inputbox input-mini"
          labelclass="tariff-label"
          required="false"
          default="'.$tariff->tariff.'"
          multiple="true">
          </field>
        </fieldset>';
        $counter++;
      }
    }

    // Add some empty tariff fields (3 by default)
    for ($i = $counter; $i <= $counter + 4; $i++) {
      $XmlStr.= '
      <fieldset name="tariffs_' . $i . '">
        <field
          id="start_date_tariff_'. $i .'" 
          name="start_date"
          type="text"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_DESC"
          class="inputbox tariff_date input-small"
          labelclass="tariff-label"
          required="false"
          multiple="true"
          default=""
          readonly="true">
        </field>
        
        <field
          id="end_date_tariff_'.$i.'"
          name="end_date"
          type="text"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_DESC"
          class="inputbox tariff_date input-small"
          validate=""
          labelclass="tariff-label"
          required="false"
          multiple="true"
          default=""
          readonly="true">
        </field>          
        <field  
          id="tariff_'. $i .'"
          name="tariff"
          type="text"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_DESC"
          class="inputbox input-mini"
          validate=""
          labelclass="tariff-label"
          required="false"
          default=""
          multiple="true">
        </field>
      </fieldset>';
    }    

    
    $XmlStr.='</fields></form>';   
    
    return $XmlStr;
    
  }
  
}
