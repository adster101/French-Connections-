<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class SpecialOffersModelSpecialOffer extends JModelAdmin
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
  protected function allowEdit($data = array(), $key = 'id')
  {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit');
  }

  /*
   * Override getItem so we can set the date format
   */

  public function getItem($pk = null)
  {

    if ($item = parent::getItem($pk))
    {

      $item->start_date = ($item->start_date != '0000-00-00') ? JFactory::getDate($item->start_date)->calendar('d-m-Y') : '';
      $item->end_date = ($item->end_date != '0000-00-00') ? JFactory::getDate($item->end_date)->calendar('d-m-Y') : '';
    }

    return $item;
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
  public function getTable($type = 'SpecialOffer', $prefix = 'SpecialOffersTable', $config = array())
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
    $form = $this->loadForm('com_specialoffers.specialoffers', 'specialoffer', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }
    return $form;
  }

  /* Method to preprocess the special offer edit form */

  protected function preprocessForm(JForm $form, $data)
  {

    // Get the user
    $user = JFactory::getUser();

    // If the user is authorised to edit state then we assume they have general admin rights
    if ($user->authorise('core.edit.state', 'com_specialoffers'))
    {

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

      if (!empty($data->property_id))
      {
        $form->setFieldAttribute('unit_id', 'readonly', 'true');
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
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_specialoffers.edit.specialoffer.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * 
   */
  public function publish(&$pks, $value = 1)
  {

    $publish = parent::publish($pks, $value);

    if ($publish)
    {
      // Item has been published, send a notification email to the owner
      foreach ($pks as $k => $v)
      {
        // Get the special offer details
        $item = $this->getItem($v);

        if (!$item)
        {
          return false;
        }
      }
    }
  }

  /**
   * Method to save the form data.
   *
   * @param	array	The form data.
   *
   * @return	boolean	True on success.
   * @since	1.6
   */
  public function save($data)
  {
    $app = JFactory::getApplication();
    $user = JFactory::getUser();
    // Get an instance of the unit table, so we can get the property ID...
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    $unit_table = $this->getTable('Unit', 'RentalTable');
    $table = $this->getTable();
    $send_notification_email = false;
    $date = JFactory::getDate()->toSql();

    // $this->getUnitDetail() // Method to get unit title, owner id and property id from a unit id
    // Checks needed here include
    // Only one active offer per unit
    // $offers = $this->getActiveOffers();
    // If owner on basic package then they get two special offers only...  
    // $offer_count = $this->getAvailableOffers();
    // 
    // Get the parent property id for the unit the offer is being added to
    if (!$unit_table->load($data['unit_id']))
    {
      $this->setError(JText::_('COM_SPECIAL_OFFERS_PROBLEM_CREATING_OFFER'));
      return false;
    }

    $data['property_id'] = $unit_table->property_id;

    // Set the date created timestamp
    $data['date_created'] = $date;

    // And format the dates into the correct mysql date format
    $data['start_date'] = JFactory::getDate($data['start_date'])->calendar('Y-m-d');
    $data['end_date'] = JFactory::getDate($data['end_date'])->calendar('Y-m-d');

    // Allow an exception to be thrown.
    try {
      $key = $table->getKeyName();
      $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
      $isNew = true;

      // Load the row if saving an existing record. 
      // Just do getItem here? what if new?
      if ($pk > 0)
      {
        $item = $this->getItem($pk);
      }

      // Offer already created. If not approved and being set to published then update the approved by gubbins
      if (empty($item->approved_by) && empty($item->approved_on) && !$item->published && $data['published'] == 1)
      {
        $data['approved_by'] = $user->id;
        $data['approved_date'] = $date;
        // Ensures that we only send notification email when offer is first approved.
        $send_notification_email = true;
      }

      // Store the data.
      if (!$table->save($data))
      {
        $this->setError($table->getError());
        return false;
      }

      // Clean the cache.
      $this->cleanCache();
    } catch (Exception $e) {
      $this->setError($e->getMessage());

      return false;
    }



    // Trigger email to admin user
    if ($send_notification_email)
    {
      // 
      var_dump($unit_table);die;
    }

    // Set additional messaging to notify user that offer is awaiting moderation etc.
    if (!$user->authorise('core.edit.state', 'com_specialoffers'))
    {
      $app->set;
      $app->enqueueMessage(JText::_('COM_SPECIALOFFERS_OFFER_ADDED_SUCCESS'));
    }
    
    // Gubbins needed for correct redirect of controller etc
    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }

    $this->setState($this->getName() . '.new', $isNew);
    
    
    return true;
  }

}