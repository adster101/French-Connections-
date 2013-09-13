<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelTariffs extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'UnitVersions', $prefix = 'HelloWorldTable', $config = array()) {
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
  public function getForm($data = array(), $loadData = true) {
    // Get the form.
    $form = $this->loadForm('com_helloworld.tariffs', 'tariffs', array('control' => 'jform', 'load_data' => $loadData));
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
   *
   */
  protected function loadFormData() {

    $input = JFactory::getApplication()->input;
    $property_id = $input->get('property_id', '', 'int');

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.unitversions.data', array());

    // We take this opportunity to 'massage' the data into the correct format 
    // so we can bind it to the form in getTariffsXML
    if (array_key_exists('start_date', $data) && array_key_exists('end_date', $data) && array_key_exists('tariff', $data)) {
      $tariffs = array();
      $num = count($data['start_date']);
      // Here we must have data passed in from the form validator
      // E.g. something hasn't validated correctly
      for ($i = 0; $i < $num; $i++) {
        $tmp = array();
        $tmp[] = $data['start_date'][$i];
        $tmp[] = $data['end_date'][$i];
        $tmp[] = $data['tariff'][$i];

        $tariffs[] = JArrayHelper::toObject($tmp, 'JOBject');
      }

      $data['tariffs'] = JArrayHelper::toObject($tariffs, 'JOBject');
      unset($data['start_date']);
      unset($data['end_date']);
      unset($data['tariff']);
    }

    // Need to get the tariff data into the form here...
    // If nout in session then we grab the item from the database
    if (empty($data)) {
      $data = $this->getItem();
    }

    // If data is not an array convert it from object
    if (!is_array($data)) {
      $data = $data->getProperties();
    }

    // Set the parent ID for this unit, if it's not set (e.g. for a new unit)
    if (!isset($data['property_id'])) {
      $data['property_id'] = $property_id;
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
   * 
   */
  
  public function getItem($pk = null) {
    // Initialise variables.
    $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
    $table = $this->getTable();

    if ($pk > 0) {
      // Attempt to load the row.
      $return = $table->load($pk);

      // Check for a table object error.
      if ($return === false && $table->getError()) {
        $this->setError($table->getError());
        return false;
      }
    }
    
    $properties = $table->getProperties(1);

    $properties = $this->getTariffs($pk, $properties);

    $item = JArrayHelper::toObject($properties, 'JObject');

    return $item;
  }

  /**
   * Get the tariffs for this unit
   * 
   */
  public function getTariffs($id = '', $properties = array()) {

    // get the existing tariff details for this property
    if ($id > 0) {
      $tariffsTable = $this->getTable('Tariffs', 'HelloWorldTable');
      $tariffs = $tariffsTable->load($id);
      // Check for a table object error.
      if ($tariffs === false && $table->getError()) {
        $this->setError($tariffs->getError());
        return false;
      }
    }

    $properties['tariffs'] = $tariffs;

    return $properties;
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
  protected function preprocessForm(JForm $form, $data) {
    // Generate the XML to inject into the form
    $XmlStr = $this->getTariffXml($form, $data);

    $form->load($XmlStr, true);
  }

  /**
   * getTariffXml - This function takes a form and some data to generate a set of XML form field definitions. These
   * definitions are then injected into the form so they are displayed on the tariffs admin screen.
   *
   * @param type $form
   * @param type $data
   * @return string
   */
  protected function getTariffXml($form, $data = array()) {
    // Check the format of the tariffs, if present. This is necessary as they form
    // we construct spits them out in a different data format
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form><fieldset name="tariffs">';
    $counter = 0;
    if (array_key_exists('tariffs', $data)) {
      // Loop over the existing availability first
      foreach ($data['tariffs']->getProperties() as $tariff) {
        $value = $tariff->getProperties();

        $XmlStr.= '
        <field
          id="tariff_start_date_' . $counter . '"
          name="start_date"
          type="tariff"
          default="' . $value[0] . '"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false"
         >
        </field>
        
        <field
          id="tariff_end_date_' . $counter . '"
          name="end_date"
          type="tariff"
          default="' . $value[1] . '"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>
        
        <field
          id="tariff_price_' . $counter . '"
          name="tariff"
          type="tariff"
          default="' . $value[2] . '"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>';
        $counter++;
      }
    }
    // Add some empty tariff fields (5 by default)
    for ($i = $counter; $i <= $counter + 4; $i++) {

      $XmlStr.= '
         <field
          id="tariff_start_date_' . $i . '"
          name="start_date"
          type="tariff"
          default=""
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>
        
        <field
          id="tariff_end_date_' . $i . '"
          name="end_date"
          type="tariff"
          default=""
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>
        
        <field
          id="tariff_price_' . $i . '"
          name="tariff"
          type="tariff"
          default=""
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>';
    }

    $XmlStr.='</fieldset></form>';
    return $XmlStr;
  }

}
