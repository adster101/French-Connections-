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


    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_helloworld.edit.tariffs.data', array());

    // loadFormData is called by JModelForm when loadForm is called with load_date => true.
    // That is, it gets called when the item is being loaded or when the user is being redirected after a failed save attempt.
    // Yes, it doesn't look pretty, but it seems necessary to put the submitted form data back into 
    // the format required by the getTariffsXml method below.
    // TO DO - Make this a bit prettier, neater or more elegent...
    if (array_key_exists('start_date', $data) && array_key_exists('end_date', $data) && array_key_exists('tariff', $data)) {

      $tariffs = $this->getTariffsFromPost($data);

      // Add any tariffs to the data object
      $data['tariffs'] = $tariffs;

      unset($data['start_date']);
      unset($data['end_date']);
      unset($data['tariff']);

      // Cast the whole thing to an stdObject
      $data = JArrayHelper::toObject($data);
    }
    // Need to get the tariff data into the form here...
    // If nout in session then we grab the item from the database
    if (empty($data)) {
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
   * 
   */
  public function getItem($pk = null) {

    if ($item = parent::getItem($pk)) {

      // Use the primary key (in this case unit id) to pull out any existing tariffs for this property
      $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

      $tariffs = $this->getTariffs($pk);

      // Add any tariffs to the unit data for display on the view
      $item->tariffs = $tariffs;
    }

    return $item;
  }

  /**
   * Get the tariffs for this unit
   * @param type $id
   * @param type $properties
   * @return boolean
   */
  public function getTariffs($id = '', $properties = array()) {

    $query = $this->_db->getQuery(true);
    $query->select("
        date_format(start_date, '%d-%m-%Y') as start_date, 
        date_format(end_date, '%d-%m-%Y') 
        as end_date, 
        tariff
      ");
    $query->from('#__tariffs');
    $query->where($this->_db->quoteName('unit_id') . ' = ' . $this->_db->quote($id));
    $query->where($this->_db->quoteName('end_date') . '>= now()');
    $this->_db->setQuery($query);

    try {
      $result = $this->_db->loadObjectList();
    } catch (RuntimeException $e) {
      $je = new JException($e->getMessage());
      $this->setError($je);
      return false;
    }

    return $result;
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
    $XmlStr = $this->getTariffXml($data);

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
  protected function getTariffXml($data) {

    $tariffs = $data->tariffs;

    $XmlStr = '<form><fieldset name="unit-tariffs">';
    $counter = 0;

    // Loop over the existing availability first
    foreach ($tariffs as $tariff) {


      $XmlStr.= '
        <field
          id="tariff_start_date_' . $counter . '"
          name="start_date"
          type="tariff"
          default="' . $tariff->start_date . '"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>
        
        <field
          id="tariff_end_date_' . $counter . '"
          name="end_date"
          type="tariff"
          default="' . $tariff->end_date . '"
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
          default="' . $tariff->tariff . '"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description=""
          class="inputbox tariff_date input-small"         
          labelclass=""
          readonly="false">
        </field>';
      $counter++;
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

  /**
   * 
   * @param type $data
   */
  public function save($data) {





    return true;
  }

  /*
   * 
   * 
   */

  protected function saveTariffs($unit_id = '', $data = array()) {

    // TO DO: I think that tariffs should be modelled in a separate model file. 
    // That is, look at moving getTariffXML, getTariffs, getTariffsByDay and this method 
    // to a HelloWorldModelTariffs file. This would make more logical sense and make it easier
    // to reuse those methods elsewhere (e.g. on property listing, search etc)
    // 
    // Similar could be considered to the facilities as well.
    // We need to extract tariff information here, because the tariffs are filtered via the 
    // controller validation method. Perhaps need to override the validation method for this model?

    if (!array_key_exists('start_date', $data)) {
      return true;
    }

    $tariffs = array('start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'tariff' => $data['tariff']);

    $tariffs_by_day = $this->getTariffsByDay($tariffs);

    $tariff_periods = HelloWorldHelper::getAvailabilityByPeriod($tariffs_by_day);

    // Get instance of the tariffs table
    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'HelloWorldTable', $config = array());


    // Bind the translated fields to the JTable instance	
    if (!$tariffsTable->save($unit_id, $tariff_periods)) {

      return false;
    }

    return true;
  }

  /**
   * Generates an array containing a day for each tariff period passed in via the form. Ensure that any new periods are
   * merged into the data before saving.
   *
   * Returns an array of tariffs per days based on tariff periods.
   * 
   * @param array $tariffs An array of tariffs periods as passed in via the tariffs admin screen
   * @return array An array of availability, by day. If new start and end dates are passed then these are included in the returned array
   * 
   */
  protected function getTariffsByDay($tariffs = array()) {
    // Array to hold availability per day for each day that availability has been set for.
    // This is needed as availability is stored by period, but displayed by day.
    $raw_tariffs = array();

    // Generate a DateInterval object which is re-used in the below loop
    $DateInterval = new DateInterval('P1D');

    // For each tariff period passed in first need to determine how many tariff periods there are
    $tariff_periods = count($tariffs['start_date']);

    for ($k = 0; $k < $tariff_periods; $k++) {

      $tariff_period_start_date = '';
      $tariff_period_end_date = '';
      $tariff_period_length = '';

      // Check that availability period is set for this loop. Possible that empty array elements exists as additional
      // tariff fields are added to the form in case owner wants to add additional tariffs etc
      try {

        if ($tariffs['start_date'][$k] != '' && $tariffs['end_date'][$k] != '' && $tariffs['tariff'][$k] != '') {

          // Convert the availability period start date to a PHP date object
          $tariff_period_start_date = new DateTime($tariffs['start_date'][$k]);

          // Convert the availability period end date to a date 
          $tariff_period_end_date = new DateTime($tariffs['end_date'][$k]);

          // Calculate the length of the availability period in days
          $tariff_period_length = date_diff($tariff_period_start_date, $tariff_period_end_date);

          // Loop from the start date to the end date adding an available day to the availability array for each availalable day
          for ($i = 0; $i <= $tariff_period_length->days; $i++) {

            // Add the day as an array key storing the availability status as the value
            $raw_tariffs[date_format($tariff_period_start_date, 'Y-m-d')] = $tariffs['tariff'][$k];

            // Add one day to the start date for each day of availability
            $date = $tariff_period_start_date->add($DateInterval);
          }
        }
      } catch (Exception $e) {
        //TO DO - Log this
      }
    }

    return $raw_tariffs;
  }

  /**
   * Function to extract the tariffs POSTed from the tariffs screen into a neater data format.
   * 
   * @param type $data
   * @return boolean
   */
  public function getTariffsFromPost($data = array()) {

    $tariffs = array();

    $num = count($data['start_date']);

    // Here we must have data passed in from the form validator
    // E.g. something hasn't validated correctly
    for ($i = 0; $i < $num; $i++) {
      $tmp = array();
      $tmp['start_date'] = $data['start_date'][$i];
      $tmp['end_date'] = $data['end_date'][$i];
      $tmp['tariff'] = $data['tariff'][$i];

      $tariffs[] = JArrayHelper::toObject($tmp);
    }



    return $tariffs;
  }

}

