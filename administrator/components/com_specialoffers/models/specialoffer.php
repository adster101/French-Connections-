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
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', $this->option);
	}
  
  /*
   * Function get offer
   * Gets one special offer for a property 
   * 
   * params
   * @id; property id
   * 
   */

  public function getActiveOffer($unit_id = null, $start_date = '', $end_date = '', $pk = 0)
  {
    $query = $this->_db->getQuery(true);
    $query->select('count(*) as count');
    $query->from($this->_db->quoteName('#__special_offers'));
    $query->where('unit_id = ' . (int) $unit_id);
    $query->where('published = 1');
    // If new start date falls between any eisting start and end dates then it can't be valid
    // http://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
    $query->where('(start_date <= ' . $this->_db->Quote($end_date) . ') AND (end_date >= ' . $this->_db->Quote($start_date) . ')');
    $query->where('id <> ' . (int) $pk);
    // Get the offer 
    $this->_db->setQuery($query);

    try {

      $result = $this->_db->loadObject();

      if ($result->count > 0)
      {
        return true;
      }
      else
      {
        return false;
      }

      return $result;
    } catch (RuntimeException $e) {
      $je = new JException($e->getMessage());
      $this->setError($je);
      return false;
    }
  }

  /**
   * Method to get a single record.
   * Overloaded simply to format the dates accordingly
   *
   * @param   integer  $pk  The id of the primary key.
   *
   * @return  mixed    Object on success, false on failure.
   *
   * @since   12.2
   */
  public function getItem($pk = null)
  {

    if ($item = parent::getItem($pk))
    {
      $item->start_date = (empty($item->start_date)) ? '' : JFactory::getDate($item->start_date)->calendar('d-m-Y');
      $item->end_date = (empty($item->end_date)) ? '' : JFactory::getDate($item->end_date)->calendar('d-m-Y');
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
   * Method to get some basic unit details for use in the confirmation email
   *  
   * @param int $id
   * @return Object on success, false on failure.
   */
  private function getUnitDetail($unit_id = '')
  {

    try {

      $query = $this->_db->getQuery(true);

      $query->select('
        b.unit_title,
        b.property_id,
        b.unit_id,
        e.firstname,
        e.surname,
        f.email
      ');

      $query->from($this->_db->quoteName('#__unit', 'a'));
      $query->leftJoin($this->_db->quoteName('#__unit_versions', 'b') . ' ON (b.unit_id = a.id and b.id = (select max(c.id) from ' . $this->_db->quoteName('#__unit_versions', 'c') . ' where c.unit_id = a.id and c.review = 0))');
      $query->leftJoin($this->_db->quoteName('#__property', 'd') . ' on d.id = b.property_id');
      $query->leftJoin($this->_db->quoteName('#__user_profile_fc', 'e') . ' on e.user_id = d.created_by');
      $query->leftJoin($this->_db->quoteName('#__users', 'f') . ' on f.id = e.user_id');
      $query->where('a.id = ' . (int) $unit_id);

      $this->_db->setQuery($query);

      $row = $this->_db->loadObject();
    } catch (Exception $e) {
      $this->setError($e->getMessage());
      return false;
    }

    return $row;
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
    $table = $this->getTable();
    $send_notification_email = false;
    $date = JFactory::getDate()->toSql();
    $unit_id = $data['unit_id'];
    $start_date = JFactory::getDate($data['start_date'])->calendar('Y-m-d');
    $end_date = JFactory::getDate($data['end_date'])->calendar('Y-m-d');
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $params = JComponentHelper::getParams('com_specialoffers');

    try {

      $unit_detail = $this->getUnitDetail($unit_id);

      if (!$unit_detail)
      {
        $this->setError(JText::_('COM_SPECIALOFFERS_OFFER_NO_UNIT'));
        return false;
      }

      // Set the parent property id for the unit the offer is being added to 
      $data['property_id'] = $unit_detail->property_id;

      // Only allow one active offer per unit
      $active_offer = $this->getActiveOffer($unit_id, $start_date, $end_date, $pk);
      if ($active_offer)
      {
        $this->setError(JText::_('COM_SPECIALOFFERS_OFFER_ALREADY_ACTIVE'));
        return false;
      }
      // If owner on basic package then they get two special offers only...  
      // $offer_count = $this->getAvailableOffers();
      // $this->getPackageType
      // Set the date created timestamp
      $data['date_created'] = $date;

      // And format the dates into the correct mysql date format
      $data['start_date'] = JFactory::getDate($data['start_date'])->calendar('Y-m-d');
      $data['end_date'] = JFactory::getDate($data['end_date'])->calendar('Y-m-d');

      // Load the row if saving an existing record. 
      // Just do getItem here? what if new?
      if ($pk > 0)
      {
        $offer = $this->getItem($pk);
      }
      else
      {
        $table = $this->getTable();

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $offer = JArrayHelper::toObject($properties, 'JObject');
      }

      // Offer already created. If not approved and being set to published then update the approved by gubbins
      if (empty($offer->approved_by) && empty($offer->approved_on) && !$offer->published && $data['published'] == 1)
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
      // Load the user details (already valid from table check).
      $fromUser = $app->getCfg('mailfrom');

      // Get the owners email, setting up to go to site mailfrom is debug is on
      $toUser = (JDEBUG) ? $app->getCfg('mailfrom') : $unit_detail->email;
      
      // The url to link to the owners property in the confirmation email
      $siteURL = JUri::root() . 'listing/' . $table->property_id . '?unit_id=' . (int) $table->unit_id;
      $intasure = $params->get('intasure');
      
      // Prepare the email.
      $subject = htmlspecialchars(JText::sprintf('COM_SPECIALOFFERS_NEW_OFFER_CONFIRMATION_SUBJECT', $unit_detail->property_id, $unit_detail->firstname), ENT_QUOTES, 'UTF-8');
      $msg = JText::sprintf('COM_SPECIALOFFERS_NEW_OFFER_CONFIRMATION_BODY', htmlspecialchars($unit_detail->firstname, ENT_QUOTES, 'UTF-8'), htmlspecialchars($unit_detail->unit_title, ENT_QUOTES, 'UTF-8'), $siteURL, $intasure);
      JFactory::getMailer()->sendMail($fromUser, $fromUser, $toUser, $subject, $msg, true);
    }

    // Set additional messaging to notify user that offer is awaiting moderation etc.
    if (!$user->authorise('core.edit.state', 'com_specialoffers'))
    {
      $app->enqueueMessage(JText::_('COM_SPECIALOFFERS_OFFER_ADDED_SUCCESS'));
    }

    // Gubbins needed for correct redirect of controller
    // Needed here as we've overloaded the save method
    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }

    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }

}