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

  public function canceloffer($id = null)
  {

    $table = $this->getTable();
    $item = $this->getItem($id);

    if (!$item)
    {
      return false;
    }

    // Update the end_date for the offer
    $item->end_date = JFactory::getDate('-1 day')->toSql();

    try
    {
      $table->save($item);
    }
    catch (Exception $e)
    {
      return false;
    }

    return true;
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

    try
    {

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
    }
    catch (RuntimeException $e)
    {
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
      //$item->start_date = (empty($item->start_date)) ? '' : JFactory::getDate($item->start_date)->calendar('d-m-Y');
      //$item->end_date = (empty($item->end_date)) ? '' : JFactory::getDate($item->end_date)->calendar('d-m-Y');
    }

    return $item;
  }

  public function getTotalOffers($property_id = '', $expiry_date = '')
  {
    $query = $this->_db->getQuery(true);
    $query->select('count(*) as count');
    $query->from($this->_db->quoteName('#__special_offers'));
    $query->where('property_id = ' . (int) $property_id);
    $query->where('published = 1');
    $query->where('start_date >= SUBDATE(' . $this->_db->Quote($expiry_date) . ', INTERVAL 1 YEAR)' );
    $query->where('start_date <= ' . $this->_db->Quote($expiry_date));
    
    // Get the offers 
    $this->_db->setQuery($query);

    try
    {

      $result = $this->_db->loadObject();

      $total = $result->count;
      
      return $total;
    }
    catch (RuntimeException $e)
    {
      $je = new JException($e->getMessage());
      $this->setError($je);
      return false;
    }
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

    try
    {

      $query = $this->_db->getQuery(true);

      $query->select('
        b.unit_title,
        b.property_id,
        b.unit_id,
        d.expiry_date,
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
    }
    catch (Exception $e)
    {
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

  protected function preprocessData($context, &$data)
  {
    parent::preprocessData($context, $data);

    // This just formats the dates correctly for display in the edit form...
    if (!is_array($data))
    {
      $data->start_date = (empty($data->start_date)) ? '' : JFactory::getDate($data->start_date)->calendar('d-m-Y');
      $data->end_date = (empty($data->end_date)) ? '' : JFactory::getDate($data->end_date)->calendar('d-m-Y');
    }
  }

  /* Method to preprocess the special offer edit form */

  protected function preprocessForm(JForm $form, $data)
  {

    // Get the user
    $user = JFactory::getUser();

    // If the user is authorised to edit state then we assume they have general admin rights
    if ($user->authorise('core.edit.state', 'com_specialoffers'))
    {

      $form->setFieldAttribute('unit_id', 'type', 'unit');

      if (!empty($data->property_id))
      {
        $form->setFieldAttribute('unit_id', 'readonly', 'true');
      }
    }
    else
    {
      // Remove these fields for non authed user groups.
      $form->removeField('published');
      $form->removeField('approved_by');
      $form->removeField('approved_date');
      $form->removeField('id');
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

    $this->preprocessData('com_specialoffer.edit', $data);

    return $data;
  }

  /**
   * Updates the status and then checks the status of the offer and if being published for the first 
   * time triggers and email to the owner.
   * 
   * @param type $pks
   * @param type $value
   * @return boolean
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

        $user = JFactory::getUser();

        // Offer already created. If not approved and being set to published then update the approved by gubbins
        if (empty($item->approved_by) && empty($item->approved_on) && $value == 1)
        {
          $item->approved_by = $user->id;
          $item->approved_date = $date;

          $table = $this->getTable();

          // Update the offer with the approved by and approved date.
          if (!$table->save($item))
          {
            $this->setError($table->getError());
            return false;
          }

          // Get the unit detail
          $unit_detail = $this->getUnitDetail($item->unit_id);

          // Send notification email when offer is first approved.
          $this->sendNotificationEmail($unit_detail, $unit_detail->property_id, $unit_detail->unit_id, $item->start_date);
        }
      }

      return true;
    }
  }

  /**
   * Method to save the form data.
   * TO DO - Move getActiveOffer() and getAvailableOffers() to the table class 
   * and implement them in check() method. Although the above method will then call those checks 
   * again?
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
    $isNew = true;

    try
    {

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
      $offer_count = $this->getTotalOffers($unit_detail->property_id, $unit_detail->expiry_date);

      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
      $model = JModelLegacy::getInstance('Listing', 'RentalModel', $config = array('ignore_request' => true));
      $model->setState('com_rental.listing.id', $unit_detail->property_id);
      $model->setState('com_rental.listing.latest', false);
      $units = $model->getItems();
      $images = $model->getTotalImages($units);

      if (!$images)
      {
        $message = JText::_('COM_SPECIALOFFERS_PROBLEM_GETTING_PROPERTY_LISTING_DETAIL');
        Throw new Exception($message);
      }
      
      if ($images < 8 && $offer_count > 2)
      {    
        $message = JText::sprintf('COM_SPECIALOFFERS_UPGRADE_REQUIRED_BEFORE_ADDING_MORE_OFFERS', $unit_detail->property_id);
        $app->enqueueMessage($message, 'notice');
        $app->redirect('index.php?option=com_specialoffers');
      }
  

      $data['date_created'] = $date;

      // And format the dates into the correct mysql date format
      $data['start_date'] = JFactory::getDate($data['start_date'])->calendar('Y-m-d');
      $data['end_date'] = JFactory::getDate($data['end_date'])->calendar('Y-m-d');

      // Load the row if saving an existing record. 
      if ($pk > 0)
      {
        $offer = $this->getItem($pk);
        $isNew = false;
      }
      else
      {
        $table = $this->getTable();

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $offer = JArrayHelper::toObject($properties, 'JObject');
      }

      if (empty($offer->created_by))
      {
        $data['created_by'] = $user->id;
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
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());
      return false;
    }

    // Trigger email to admin user
    if ($send_notification_email)
    {
      $this->sendNotificationEmail($unit_detail, $table->property_id, $table->unit_id, $table->start_date);
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

  public function sendNotificationEmail($unit_detail, $property_id, $unit_id, $start_date)
  {
    $app = JFactory::getApplication();
    $params = JComponentHelper::getParams('com_specialoffers');

    // Load the user details (already valid from table check).
    $fromUser = $app->getCfg('mailfrom');

    // Get the owners email, setting up to go to site mailfrom is debug is on
    $toUser = (JDEBUG) ? $app->getCfg('mailfrom') : $unit_detail->email;

    // The url to link to the owners property in the confirmation email
    $siteURL = JUri::root() . 'listing/' . $property_id . '?unit_id=' . (int) $unit_id;
    $intasure = $params->get('intasure');

    $start_date = JHtml::date($start_date, 'd m Y');

    // Prepare the email.
    $subject = htmlspecialchars(JText::sprintf('COM_SPECIALOFFERS_NEW_OFFER_CONFIRMATION_SUBJECT', $unit_detail->property_id, $unit_detail->firstname), ENT_QUOTES, 'UTF-8');
    $msg = JText::sprintf('COM_SPECIALOFFERS_NEW_OFFER_CONFIRMATION_BODY', htmlspecialchars($unit_detail->firstname, ENT_QUOTES, 'UTF-8'), htmlspecialchars($unit_detail->unit_title, ENT_QUOTES, 'UTF-8'), $start_date, $siteURL, $intasure);
    JFactory::getMailer()->sendMail($fromUser, $fromUser, $toUser, $subject, $msg, true);
  }
}