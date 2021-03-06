<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelTariffs extends JModelAdmin
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
    public function getTable($type = 'UnitVersions', $prefix = 'RentalTable', $config = array())
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
        $form = $this->loadForm('com_rental.tariffs', 'tariffs', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_rental.edit.tariffs.data', array());

        // loadFormData is called by JModelForm when loadForm is called with load_date => true.
        // That is, it gets called when the item is being loaded or when the user is being redirected after a failed save attempt.
        // Yes, it doesn't look pretty, but it seems necessary to put the submitted form data back into
        // the format required by the getTariffsXml method below.
        // TO DO - Make this a bit prettier, neater or more elegent...use JInput here...
        if (array_key_exists('start_date', $data) && array_key_exists('end_date', $data) && array_key_exists('tariff', $data))
        {

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
     *
     */
    public function getItem($pk = null)
    {

        if ($item = parent::getItem($pk))
        {

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
    public function getTariffs($id = '', $show_expired = true)
    {

        $date = JHtml::date('now', 'Y-m-d');

        $query = $this->_db->getQuery(true);
        $query->select("
        date_format(start_date, '%d-%m-%Y') as start_date,
        date_format(end_date, '%d-%m-%Y')
        as end_date,
        tariff
      ");
        $query->from('#__tariffs');
        $query->where($this->_db->quoteName('unit_id') . ' = ' . $this->_db->quote($id));

        $this->_db->setQuery($query);

        if (!$show_expired)
        {
            $query->where('end_date >= ' . $this->_db->quote($date));
        }

        try
        {
            $result = $this->_db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
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
    protected function preprocessForm(JForm $form, $data)
    {
        // Generate the XML to inject into the form
        $XmlStr = $this->getTariffXml($data);

        $form->setFieldAttribute('copy_tariffs', 'property_id', $data->property_id);
        $form->setFieldAttribute('copy_tariffs', 'unit_id', $data->unit_id);

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
    protected function getTariffXml($data)
    {

        $tariffs = $data->tariffs;

        $XmlStr = '<form><fieldset name="unit-tariffs">';
        $counter = 0;

        // Loop over the existing availability first
        foreach ($tariffs as $tariff)
        {


            $XmlStr.= '
        <field
          id="tariff_start_date_' . $counter . '"
          name="start_date"
          type="tariff"
          multiple="true"
          default="' . $tariff->start_date . '"
          label="COM_RENTAL_AVAILABILITY_FIELD_START_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small">
        </field>

        <field
          id="tariff_end_date_' . $counter . '"
          name="end_date"
          type="tariff"
          multiple="true"
          default="' . $tariff->end_date . '"
          label="COM_RENTAL_AVAILABILITY_FIELD_END_DATE_LABEL"
          description=""
          class="inputbox tariff_date input-small">
        </field>

        <field
          id="tariff_price_' . $counter . '"
          name="tariff"
          type="tariff"
          multiple="true"
          default="' . $tariff->tariff . '"
          label="COM_RENTAL_TARIFFS_FIELD_TARIFF_LABEL"
          description=""
          filter="RentalHelper::filterTariffs"
          class="inputbox tariff_date input-small">
        </field>';
            $counter++;
        }


        // Add some empty tariff fields (5 by default)
        for ($i = $counter; $i <= $counter + 4; $i++)
        {

            $XmlStr.= '
         <field
          id="tariff_start_date_' . $i . '"
          name="start_date"
          type="tariff"
          multiple="true"
          label="COM_RENTAL_AVAILABILITY_FIELD_START_DATE_LABEL"
          class="inputbox tariff_date input-small"
          hint="dd-mm-yyyy">
        </field>

        <field
          id="tariff_end_date_' . $i . '"
          name="end_date"
          type="tariff"
          multiple="true"
          label="COM_RENTAL_AVAILABILITY_FIELD_END_DATE_LABEL"
          class="inputbox tariff_date input-small"
          hint="dd-mm-yyyy">
        </field>

        <field
          id="tariff_price_' . $i . '"
          name="tariff"
          type="tariff"
          multiple="true"
          hint="e.g. 275"
          label="COM_RENTAL_TARIFFS_FIELD_TARIFF_LABEL"
          filter="RentalHelper::filterTariffs"
          class="inputbox tariff_date input-small">
        </field>';
        }

        $XmlStr.='</fieldset></form>';
        return $XmlStr;
    }

    /**
     * Method to save tariffs and additional tariff info.
     *
     * @param type $data
     */
    public function save($data = array())
    {

        // Get the relevant data up front
        $table = $this->getTable('Tariffs', 'RentalTable');

        // Get the date time now
        $now = new DateTime();

        // If there is a unit ID set in copy tariffs then load that data up!

        if (!empty($data['copy_tariffs']))
        {
            $unit_id = (int) $data['copy_tariffs'];
            $tariffs = $this->getTariffs($unit_id);
        }
        else
        {
            $tariffs = $this->getTariffsFromPost($data);
        }
        $model = $this->getInstance('UnitVersions', 'RentalModel');
        $pk = ($data['unit_id']) ? $data['unit_id'] : '';
        $tariffs_by_day = $this->getTariffsByDay($tariffs);
        $tariff_periods = RentalHelper::getAvailabilityByPeriod($tariffs_by_day, 'tariff');
        $unit_data = array(); // An array to hold data about the base unit to update
        $from_price = false; // Holds the minimum price for a unit based on the set of tariffs being saved.
        $to_price = false; // Holds the maximum price for a unit based on the set of tariffs being saved.

        // We've checked all tariffs, need to save 'em
        // Generate a logger instance for tariffs
        JLog::addLogger(array('text_file' => 'tariffs.update.php'), 'DEBUG', array('tariffs'));

        // Get an db instance and start a transaction
        $db = JFactory::getDBO();
        $db->transactionStart();

        try
        {

            JLog::add('About to delete tariffs for unit ' . $pk, 'DEBUG', 'tariffs');

            //Delete existing tariffs for this unit
            $table->delete($pk);

            JLog::add('Tariffs deleted for unit ' . $pk, 'DEBUG', 'tariffs');

            // Set the Tariff table key to id. Sigh, we do this so that the save method inserts a new record
            // against the id rather than trying to update the unit_id record, which we've just deleted.
            //$table->set('_tbl_key', 'id');
            $table->set('_tbl_keys', array('id'));

            // Save each, which also binds, checks and stores the tariff
            foreach ($tariff_periods as $tariff_period)
            {

                $tariff_period[unit_id] = $pk;
                $tariff_period['id'] = '';

                JLog::add('About to save tariff ( ' . $tariff_period['start_date'] . ' - ' . $tariff_period['end_date'] . ' - ' . $tariff_period['tariff'] . ' for unit ' . $pk, 'DEBUG', 'tariffs');

                // Check that each tariff is valid
                if (!$table->save($tariff_period))
                {

                    // Log it baby
                    JLog::add('Problem saving above tariff for unit' . $pk, 'DEBUG', 'tariffs');

                    // Rollback any db changes
                    $db->transactionRollback();

                    // Set an error message
                    $this->setError(JText::_('COM_RENTAL_HELLOWORLD_TARIFFS_TARIFF_START_DATE'));
                    return false;
                }

                // This bit compares checks that the tariff end date
                // is not expired. There's no point showing a tariff that is
                // no longer current.
                $end_date = new DateTime($tariff_period['end_date']);


                if ($end_date < $now)
                {
                    continue;
                }

                if (!$from_price)
                {
                    $from_price = $tariff_period['tariff'];
                    $to_price = $tariff_period['tariff'];
                }
                else
                {
                    $from_price = ($tariff_period['tariff'] < $from_price) ? $tariff_period['tariff'] : $from_price;
                    $to_price = ($tariff_period['tariff'] > $to_price) ? $tariff_period['tariff'] : $to_price;
                }


                // Flush the table ready for the next lot...
                $table->reset();
            }

            // Commit the transaction
            $db->transactionCommit();
        }
        catch (Exception $e)
        {

            // Roll back any queries executed so far
            $db->transactionRollback();

            $this->setError($e->getMessage());

            // Log the exception
            JLog::add('There was a problem: ' . $e->getMessage(), 'DEBUG', 'tariffs');
            return false;
        }

        // Tariffs are saved, now save the rest of the unit information by handing it off to the unitversions model
        unset($data['start_date']);
        unset($data['end_date']);
        unset($data['tariff']);

        // // If the base currency is EUR then calculate the correct price in sterling
        // if ($data['base_currency'] == 'EUR')
        // {
        //     $from_prices = (!empty($from_price)) ? JHtmlGeneral::price($from_price, 'EUR') : '';
        //     $to_prices = (!empty($to_price)) ? JHtmlGeneral::price($to_price, 'EUR') : '';
        //
        //     $from_price = $from_prices['GBP'];
        //     $to_price = $to_prices['GBP'];
        // }
        // Get an instance of the unit model
        $unit = JModelLegacy::getInstance('Unit', 'RentalModel');

        // Set the data and save the from price against the unit
        $unit_data['from_price'] = $from_price;
        $unit_data['to_price'] = $to_price;
        $unit_data['id'] = $pk;

        $unit->save($unit_data);

        // Proceed and save the rest of the submitted data against the unit version (creating a new one if necessary)
        $blah = $model->save($data);

        // Set the table key back to unit_id
        //$table->set('_tbl_key', 'unit_id');
        $table->set('_tbl_keys', array('unit_id'));

        $pkName = $table->getKeyName();

        $table->unit_id = $data['unit_id'];

        // Important - need to set the model state here so that the controller redirects accordingly.
        if (isset($table->$pkName))
        {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }

    /*
     *
     *
     */

    protected function saveTariffs($unit_id = '', $data = array())
    {

        // Similar could be considered to the facilities as well.
        // We need to extract tariff information here, because the tariffs are filtered via the
        // controller validation method. Perhaps need to override the validation method for this model?

        if (!array_key_exists('start_date', $data))
        {
            return true;
        }

        $tariffs = array('start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'tariff' => $data['tariff']);



        // Get instance of the tariffs table
        $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable', $config = array());


        // Bind the translated fields to the JTable instance
        if (!$tariffsTable->save($unit_id, $tariff_periods))
        {

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
    protected function getTariffsByDay($tariffs = array())
    {
        // Array to hold availability per day for each day that availability has been set for.
        // This is needed as availability is stored by period, but displayed by day.
        $raw_tariffs = array();

        // Generate a DateInterval object which is re-used in the below loop
        $DateInterval = new DateInterval('P1D');


        foreach ($tariffs as $tariff)
        {

            $tariff_period_start_date = '';
            $tariff_period_end_date = '';
            $tariff_period_length = '';

            // Check that availability period is set for this loop. Possible that empty array elements exists as additional
            // tariff fields are added to the form in case owner wants to add additional tariffs etc
            try
            {

                if ($tariff->start_date != '' && $tariff->end_date != '' && $tariff->tariff != '')
                {

                    // Convert the availability period start date to a PHP date object
                    $tariff_period_start_date = new DateTime($tariff->start_date);

                    // Convert the availability period end date to a date
                    $tariff_period_end_date = new DateTime($tariff->end_date);

                    // Calculate the length of the availability period in days
                    $tariff_period_length = date_diff($tariff_period_start_date, $tariff_period_end_date);

                    // Loop from the start date to the end date adding an available day to the availability array for each availalable day
                    for ($i = 0; $i <= $tariff_period_length->days; $i++)
                    {

                        // Add the day as an array key storing the availability status as the value
                        $raw_tariffs[date_format($tariff_period_start_date, 'Y-m-d')] = $tariff->tariff;

                        // Add one day to the start date for each day of availability
                        $date = $tariff_period_start_date->add($DateInterval);
                    }
                }
            }
            catch (Exception $e)
            {
                //TO DO - Log this
            }
        }

        ksort($raw_tariffs);

        return $raw_tariffs;
    }

    /**
     * Function to extract the tariffs POSTed from the tariffs screen into a neater data format.
     *
     * @param type $data
     * @return boolean
     */
    public function getTariffsFromPost($data = array())
    {

        $tariffs = array();

        $num = count($data['start_date']);

        // Here we must have data passed in from the form validator
        // E.g. something hasn't validated correctly
        for ($i = 0; $i < $num; $i++)
        {
            $tmp = array();
            $tmp['start_date'] = $data['start_date'][$i];
            $tmp['end_date'] = $data['end_date'][$i];
            $tmp['tariff'] = $data['tariff'][$i];

            $tariffs[] = JArrayHelper::toObject($tmp);
        }



        return $tariffs;
    }

}
