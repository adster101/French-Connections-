<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelAutoRenewals extends JModelAdmin
{

  public $extension = 'com_rental';

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Property', $prefix = 'RentalTable', $config = array())
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
    $form = $this->loadForm('com_rental.helloworld', 'autorenewals', array('control' => 'jform', 'load_data' => $loadData));
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
    $data = JFactory::getApplication()->getUserState('com_rental.property.renewal.data', array());

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


    if (!empty($data->VendorTxCodes))
    {

      $default = ($data->VendorTxCode) ? $data->VendorTxCode : '';

      // Build an XML string to inject additional fields into the form
      $XmlStr = '<form><fieldset name="autorenewaloptions" description="COM_RENTAL_HELLOWORLD_AUTORENEWAL_BLURB" label="COM_RENTAL_HELLOWORLD_AUTORENEWAL_LEGEND">';
      $XmlStr .= '<field
              name="VendorTxCode"
              type="radio"
              label="COM_RENTAL_HELLOWORLD_AUTO_RENEWAL_CAPTION"
              description =""
              required="true"
              default="' . $default . '"
              class="validate required">';

      // Loop over the existing availability first
      foreach ($data->VendorTxCodes as $transaction)
      {

        $XmlStr.= '<option value="' . $transaction->id . '"> ' .
                JText::sprintf('COM_RENTAL_HELLOWORLD_AUTORENEWAL_CARD_DETAILS', $transaction->CardType, $transaction->CardLastFourDigits, $transaction->CardExpiryDate) .
                ' </option>';
      }
      $XmlStr .= '<option value=\'0\'>None/opt out</option>';
      $XmlStr .= '</field></fieldset></form>';
      $form->load($XmlStr, false);
    }
  }

  /*
   * Augmented getItem function to get the existing transactions for a listing
   * so that we can add them to the form
   *
   */

  public function getItem($pk = null)
  {


    if ($item = parent::getItem($pk))
    {

      // Need to grab any existing transactions for this property listing
      $transactions = $this->getProtxTransactions($item->id);

      $item->VendorTxCodes = $transactions;
    }

    return $item;
  }

  /*
   * Get a list of the protx transactions we have on file for this listing
   *
   */

  protected function getProtxTransactions($pk = null)
  {

    if (!$pk)
    {
      return false;
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('
      CardLastFourDigits,
      id,
      VendorTxCode,
      CardType,
      date_format(CardExpiryDate, "%d %M %Y") as CardExpiryDate

    ');

    $query->from('#__protx_transactions');

    $query->where('property_id = ' . (int) $pk);
    $query->where($db->quoteName('Status') . ' = ' . $db->quote('OK'));
    $query->order('id', 'asc');
    $db->setQuery($query);

    $rows = $db->loadObjectList();

    if (!$rows)
    {
      return false;
    }

    return $rows;
  }

  /**
   * Method to save the form data.
   *
   * @param   array  $data  The form data.
   *
   * @return  boolean  True on success, False on error.
   *
   * @since   12.2
   */
  public function save($data)
  {
    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable('Property', 'RentalTable');
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try
    {
      // Load the row if saving an existing record.
      if ($pk > 0)
      {
        $table->load($pk);
        $isNew = false;
      }

      // Bind the data.
      if (!$table->bind($data))
      {
        $this->setError($table->getError());
        return false;
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Check the data.
      if (!$table->check())
      {
        $this->setError($table->getError());
        return false;
      }

      // Trigger the onContentBeforeSave event.
      $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
      if (in_array(false, $result, true))
      {
        $this->setError($table->getError());
        return false;
      }

      // Store the data.
      if (!$table->store())
      {
        $this->setError($table->getError());
        return false;
      }

      // Clean the cache.
      $this->cleanCache();

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());
      return false;
    }

    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }

}
