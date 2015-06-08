<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class RealestateModelListing extends JModelForm
{

  /**
   * @var object item
   */
  protected $item;

  /*
   * The Item ID of the menu item
   */
  public $itemid = '';
  
  /**
   * @var boolean review
   */
  public $preview = false;

  public function __construct($config = array())
  {

    parent::__construct($config = array());

    $input = JFactory::getApplication()->input;

    $this->preview = ($input->get('preview', 0, 'boolean')) ? true : false;
    
    $this->itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));
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
  public function getTable($type = 'Enquiry', $prefix = 'EnquiriesTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the contact form.
   *
   * The base form is loaded from XML and then an event is fired
   *
   *
   * @param	array	$data		An optional array of data for the form to interrogate.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	JForm	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    $form = $this->loadForm('enquiry', 'enquiry', array('control' => 'jform', 'load_data' => $loadData));

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
    $data = JFactory::getApplication()->getUserState('com_accommodation.enquiry.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState()
  {

    // Get the input values etc
    $app = JFactory::getApplication('site');
    $input = $app->input;

    // Get the property id
    $id = $input->get('id', '', 'int');

    // Set the states
    $this->setState('property.id', $id);

    parent::populateState();
  }

  /**
   * Get the property listing details. This comprises of the main property and the unit. If no unit specified the first based on unit ordering is used...
   *
   * @return object The message to be displayed to the user
   */
  public function getItem()
  {


    // Get the state for this property ID
    $id = $this->getState('property.id');

    $select = '
        a.id as property_id,
        b.agency_reference,
        b.realestate_property_id,
        b.latitude,
        b.longitude,
        b.country,
        b.area,
        b.region,
        b.department,
        b.city,
        b.use_invoice_details,
        b.first_name as alt_first_name,
        b.surname as alt_surname,
        b.email_1 as alt_email_1,
        b.phone_1 as alt_phone_1,
        b.phone_2 as alt_phone_2,
        b.description,
        b.title,
        b.single_bedrooms,
        b.double_bedrooms,
        b.bathrooms,
        b.additional_price_notes,
        b.base_currency,
        b.price,
        e.title as city_title,
        f.title as department,
        g.title as region,
        ufc.sms_alert_number,
        ufc.sms_valid,
        ufc.sms_nightwatchman,
        air.name as airport,
        air.code as airport_code,
        air.id as airport_id,
        ufc.phone_1, 
        ufc.phone_2, 
        ufc.phone_3,
        ufc.firstname,
        ufc.surname,
        ufc.email_alt,
        ufc.exchange_rate_eur,
        ufc.exchange_rate_usd,
        ufc.address1,
        ufc.address2,
        ufc.email_alt,
        ufc.city as owner_city,
        ufc.region as county,
        ufc.country,
        ufc.postal_code';

    $query = $this->_db->getQuery(true);

    $query->select($select);

    $query->from('#__realestate_property as a');

    if (!$this->preview)
    {
      $query->leftJoin('#__realestate_property_versions b ON (b.realestate_property_id = a.id and b.id = (select max(d.id) from #__realestate_property_versions as d where d.realestate_property_id = a.id and d.review = 0))');
    }
    else
    {
      $query->leftJoin('#__realestate_property_versions b ON (b.realestate_property_id = a.id and b.id = (select max(d.id) from #__realestate_property_versions as d where d.realestate_property_id = a.id))');
    }

    // Join the attributes a second time to get at the accommodation type
    $query->leftJoin('#__classifications e ON e.id = b.city');
    $query->leftJoin('#__classifications f ON f.id = b.department');
    $query->leftJoin('#__classifications g ON g.id = b.region');

    $query->leftJoin('#__users u on a.created_by = u.id');
    $query->leftJoin('#__user_profile_fc ufc on u.id = ufc.user_id');

    $query->leftJoin('#__airports air on air.id = b.airport');

    // Refine the query based on the various parameters
    $query->where('a.id=' . (int) $id);

    if (!$this->preview)
    {
      $query->where('a.published = 1');
      $query->where('b.review = 0');
    }
    else
    {
      $query->where('b.review in (0,1)');
    }

    if (!$this->preview)
    {
      // TO DO: We should check the expiry date at some point.
      $query->where('a.expiry_date >= ' . $this->_db->quote(JFactory::getDate()->calendar('Y-m-d')));
    }

    try
    {

      $this->item = $this->_db->setQuery($query)->loadObject();
    }
    catch (Exception $e)
    {
      // TO DO - Log me baby
    }

    if (empty($this->item))
    {
      // This property has expired or is otherwise unavailable.                
      return false;
    }


    if (!empty($this->item->city))
    {
      $this->item->city = trim(preg_replace('/\(.*?\)/', '', $this->item->city));
    }

    return $this->item;
  }

  /**
   * Function to get maps items to show on the location map.
   * At present, is only 'places of interest'
   * 
   */
  public function getMapItems($lat = '', $lon = '')
  {

    $db = JFactory::getDbo();
    $app = JFactory::getApplication('site');
    $menus = $app->getMenu();
    $items = $menus->getItems('component', 'com_placeofinterest');
    $items = is_array($items) ? $items : array();
    $itemid = $items[0]->id;
    $query = $db->getQuery(true);

    $query->select("id, left(description, 125) as description, title, latitude, longitude, alias");

    $query->from('#__places_of_interest a');

    $query->where('
        ( 3959 * acos(cos(radians(' . $lat . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $lon . '))
          + sin(radians(' . $lat . '))
          * sin(radians(a.latitude))) < 50)
        ');

    $db->setQuery($query);
    $rows = $db->loadObjectList();
    foreach ($rows as $k => $v)
    {
      $rows[$k]->description = JHtml::_('string.truncate', $v->description, 75, true, false);
      $rows[$k]->link = JRoute::_('index.php?option=com_placeofinterest&Itemid=' . $itemid . '&place=' . $v->alias);
    }

    return $rows;
  }

  /**
   * Gets a list of related properties based on the property someone has just enquired on.
   * 
   * @return boolean
   */
  public function getRelatedProps()
  {

    if (empty($this->item))
    {
      return false;
    }

    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_realestatesearch/models');

    $app = JFactory::getApplication();

    $input = $app->input;
    $filter = JFilterInput::getInstance();

    $location = JApplication::stringURLSafe($filter->clean($this->item->department, 'string'));

    if (!$location)
    {
      return false;
    }

    // Set s_kwds in the input data. E.g. spoof a location search...
    $app->input->set('s_kwds', $location);
    $app->input->set('limit', 4);

    $model = JModelLegacy::getInstance('Search', 'RealestateSearchModel');

    $model->getLocalInfo(); // Must call this first, probably should be a protected method called internally from the model
    $results = $model->getResults(); // Get the property listings, related to this one, if any.s

    return $results;
  }

  /*
   * Function to get a list of images for a property
   *
   */

  public function getImages()
  {

    // Get the property ID
    $id = $this->getState('property.id');

    // Do some logging
    JLog::addLogger(array('text_file' => 'property.view.php'), JLog::ALL, array('images'));
    JLog::add('Retrieving images for - ' . $id . ')', JLog::ALL, 'images');

    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      d.realestate_property_id,
      d.image_file_name,
      d.caption,
      d.ordering
    ');

    $query->from('#__realestate_property a');
    if (!$this->preview)
    {
      $query->leftJoin('#__realestate_property_versions b ON (b.realestate_property_id = a.id and b.id = (select max(c.id) from #__realestate_property_versions c where realestate_property_id = a.id and c.review = 0))');
    }
    else
    {
      $query->leftJoin('#__realestate_property_versions b ON (b.realestate_property_id = a.id and b.id = (select max(c.id) from #__realestate_property_versions c where realestate_property_id = a.id))');
    }

    $query->join('left', '#__realestate_property_images_library d on (d.realestate_property_id = a.id and d.version_id = b.id)');

    $query->where('a.id = ' . (int) $id);

    $query->order('d.ordering', 'asc');
    $db->setQuery($query);

    $images = $db->loadObjectList();




    // Check the $availability loaded correctly
    if (!$images)
    {
      // Ooops, there was a problem getting the availability
      // Check that the row actually exists
      JLog::add('Problem fetching images for - ' . $id, JLog::ERROR, 'images');

      // Log it baby...
    }

    return $images;
  }

  /*
   * Function to return the location breadcrumb trail for a property
   *
   */

  public function getCrumbs()
  {

    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    $table = JTable::getInstance('Classification', 'ClassificationTable');
    $pathArr = new stdClass(); // An array to hold the paths for the breadcrumbs trail.

    try
    {
      $path = $table->getPath($pk = $this->item->city);
    }
    catch (Exception $e)
    {

      // Log the exception here...
      return false;
    }

    array_shift($path); // Remove the first element as it's the root of the NST
    // Put the path into a std class obj which is passed into the getPathway method.
    foreach ($path as $k => $v)
    {
      if ($v->parent_id)
      {
        $city = trim(preg_replace('/\(.*?\)/', '', $v->title));

        $pathArr->$k->link = 'index.php?option=com_realestatesearch&Itemid=' . (int) $this->itemid . '&s_kwds=' . JApplication::stringURLSafe($v->title);
        $pathArr->$k->name = $city;
      }
    }

    // Add the PRN as the final element
    $total = count($path);
    $pathArr->$total->link = '';
    $pathArr->$total->name = JText::sprintf('COM_ACCOMMODATION_PROPERTY_REFERENCE', (int) $this->item->property_id);

    return $pathArr;
  }

  /**
   * Increment the hit counter for the article.
   *
   * @param	int		Optional primary key of the article to increment.
   *
   * @return	boolean	True if successful; false otherwise and internal error set.
   */
  public function hit()
  {

    $input = JFactory::getApplication()->input;
    $hitcount = $input->getInt('hitcount', 1);

    if ($hitcount)
    {
      // Get the property id
      $pk = $this->getState('property.id', false);

      $db = $this->getDbo();

      $query = $db->getQuery(true);

      $query->insert('#__property_views');

      $query->columns(array('property_id', 'date_created'));

      $date = JFactory::getDate()->toSql();

      $query->values("$pk, '$date'");

      $db->setQuery($query);

      try
      {
        $db->execute();
      }
      catch (RuntimeException $e)
      {
        $this->setError($e->getMessage());
        return false;
      }
    }
    return true;
  }

  /**
   * Function to process and send an enquiry onto an owner...
   * 
   * Also need to send an email to the holiday maker as an acknowledgement
   * 
   * Filter the message based on the banned text phrases and banned email addresses. This seems futile.
   * How easy is it to generate a new email address or alter the phrasing. 
   * More robust would be to require a user account, keep a track of how many email a user is sending in a 
   * certain timeframe and trap any for manual review above that number. Similar to what happens now. 
   * 
   * @param array $data
   * @param type $params
   * @return boolean
   */
  public function processEnquiry($data = array(), $params = '', $id = '', $unit_id = '', $override = false)
  {

    // Set up the variables we need to process this enquiry
    $app = JFactory::getApplication();
    $date = JFactory::getDate();
    $owner_email = '';
    $owner_name = '';
    $valid = true;
    jimport('clickatell.SendSMS');
    $sms_params = JComponentHelper::getParams('com_rental');
    $banned_emails = explode(',', $params->get('banned_email'));
    $banned_phrases = explode(',', $params->get('banned_text'));
    // The details of where who is sending the email (e.g. FC in this case).
    $mailfrom = $params->get('admin_enquiry_email_no_reply','');
    $fromname = $app->getCfg('fromname');
    $sitename = $app->getCfg('sitename');
    $car_hire_link = JUri::base() . JRoute::_('index.php?option=com_content&Itemid=' . (int) $params->get('car_hire_affiliate'));
    $currency_link = JUri::base() . JRoute::_('index.php?option=com_content&Itemid=' . (int) $params->get('currency_affiliate'));
    $ferry_link = JUri::base() . JRoute::_('index.php?option=com_content&Itemid=' . (int) $params->get('ferry_affiliate'));
    $minutes_until_safe_to_send = '';

    // Add enquiries paths
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_enquiries/tables');

    // Check the banned email list     
    if (in_array($data['guest_email'], $banned_emails))
    {
      $valid = false; // Naughty!
      $data['state'] = -1;
    }

    // Check the banned phrases list - This is currently done as a form field validation rule
    if ($this->contains($data['message'], $banned_phrases))
    {
      $valid = false; // Naughty!!
      $data['state'] = -3;
    }

    // Check the number of enquiries from this email address    
    if ($this->enquiryCount($data['guest_email']) >= $params->get('enqs_per_day', 10))
    {
      $valid = false; //Naughty!!!
      $data['state'] = -4;
    }

    $table = $this->getTable();

    // Set the date created timestamp
    $data['date_created'] = $date->toSql();
    $data['property_id'] = $id;
    $data['unit_id'] = $unit_id;

    // Override flag to ensure that email will get sent even if not valid. 
    // For example, from enquiry manager admin wants to sent a failed enquiry
    if ($override)
    {
      $data['state'] = 0;
      $valid = true;
    }

    // Check that we can save the data and save it out to the enquiry table
    if (!$table->save($data))
    {
      return false;
    }

    // We only need to process the rest of this is the enquiry is validated
    if ($valid)
    {
      // Need to get the contact detail preferences for this property/user combo
      $item = $this->getItem();

      // If the property is set to use invoice details
      // Override anything set in the property version 
      if ($item->use_invoice_details)
      {

        $owner_email = (JDEBUG) ? $params->get('admin_enquiry_email') : $item->email;
        // This assumes that name is in synch with the user profile table first and last name fields...
        $owner_name = htmlspecialchars($item->firstname);
      }
      else
      {
        // We just use the details from the contact page, possibly also send this to the owner...
        $owner_email = (JDEBUG) ? $params->get('admin_enquiry_email') : $item->email_1;
        $owner_name = htmlspecialchars($item->firstname) . ' ' . htmlspecialchars($item->surname);
      }

      // The details of the enquiry as submitted by the holiday maker
      $firstname = $data['guest_forename'];
      $surname = $data['guest_surname'];
      $email = $data['guest_email'];
      $phone = $data['guest_phone'];
      $message = $data['message'];
      $arrival = $data['start_date'];
      $end = $data['end_date'];
      $adults = $data['adults'];
      $children = $data['children'];
      $full_name = $firstname . ' ' . $surname;

      // Prepare email body
      $body = JText::sprintf($params->get('owner_email_realestate_enquiry_template'), $owner_name, $firstname, $surname, $email, $phone, htmlspecialchars($message, ENT_COMPAT, 'UTF-8'), $arrival, $end, $adults, $children);

      $mail = JFactory::getMailer();
      $mail->addRecipient($owner_email, $owner_name);
      $mail->addReplyTo(array($mailfrom, $fromname));
      $mail->setSender(array($mailfrom, $fromname));
      $mail->addBCC($mailfrom, $fromname);
      $mail->setSubject($sitename . ': ' . JText::sprintf('COM_REALESTATE_NEW_ENQUIRY_RECEIVED', $item->title, $id));
      $mail->setBody($body);

      // If there is a secondary email then add that as a recipient
      if (!empty($item->email_alt))
      {
        $alt_email = (JDEBUG) ? 'izzy@frenchconnections.co.uk' : $item->email_alt;
        $mail->addRecipient($alt_email, $owner_name);
      }

      if (!$mail->Send())
      {
        return false;
      }

      // Prepare email body for the holidaymaker email
      // TO DO - Make the property link not hard coded 
      $property_link = JUri::base() . 'forsale/' . (int) $id;
      $body = JText::sprintf($params->get('buyer_realestate_email_enquiry_template'), $firstname, $property_link, $property_link, $car_hire_link, $currency_link, $ferry_link);

      $mail->ClearAllRecipients();
      $mail->ClearAddresses();
      $mail->setBody($body);
      $mail->isHtml(true);
      $mail->setSubject(JText::sprintf('COM_REALESTATE_NEW_ENQUIRY_SENT', $item->title));
      $mail->addRecipient($email);

      if (!$mail->Send())
      {
        return false;
      }

      // Only fire up the SMS bit if the owner is subscribed to SMS alerts...
      if ($item->sms_valid)
      {

        $sms = new SendSMS($sms_params->get('username'), $sms_params->get('password'), $sms_params->get('id'));
        /*
         *  if the login return 0, means that login failed, you cant send sms after this 
         */
        if (!$sms->login())
        {
          return false;
        }

        // Get minutes between now and midnight
        // If minutes less than 240 
        // Schedule for tomorrow at eight
        // Else schedule for today at eight
        // Set default timezone so we can work out the correct time now
        date_default_timezone_set("Europe/London");

        // Get the time in 'HHmm' format
        // E.g. 2034
        $time = (int) date('Hi');

        if ($item->sms_nightwatchman && ($time > 2000 && $time < 2359))
        {

          // Get the unix timestamp for tomorrow at 0800h
          $tomorrow_at_eight = mktime(8, 0, 0, date('m'), date('d') + 1, date('y'));

          // Calculate the minutes between now and when we it's safe to send the message.
          $minutes_until_safe_to_send = round(($tomorrow_at_eight - time()) / 60);
        }
        elseif ($item->sms_nightwatchman && ($time > 0 && $time < 800))
        {
          // Get the unix timestamp for later today at 0800h
          $today_at_eight = mktime(8, 0, 0, date('m'), date('d'), date('y'));

          // Calculate the minutes between now and when we it's safe to send the message.
          $minutes_until_safe_to_send = round(($today_at_eight - time()) / 60);
        }

        /*
         * Send sms using the simple send() call 
         */
        if (!$sms->send($item->sms_alert_number, JText::sprintf('COM_ACCOMMODATION_NEW_ENQUIRY_RECEIVED_SMS_ALERT', $id, $full_name, $phone, $email), $minutes_until_safe_to_send))
        {
          return false;
        }
      }
    }

    // We are done.
    // TO DO: Should add some logging of the different failure points above.
    return true;
  }

  /**
   * Check through the list of banned phrases and return true if one found 
   * http://stackoverflow.com/questions/6228581/how-to-search-array-of-string-in-another-string-in-php
   * 
   * @param type $string
   * @param array $search
   * @param type $caseInsensitive
   * @return type
   */
  function contains($value, Array $banned)
  {
    foreach ($banned as $item)
    {
      if (JString::stristr($item, $value) !== false)
      {
        return true;
      }
    }

    return false;
  }

  public function enquiryCount($email = '')
  {

    // Get a new query object
    $query = $this->_db->getQuery(true);

    $query->select('count(' . $this->_db->quoteName('guest_email') . ') as count');
    $query->from($this->_db->quoteName('#__enquiries'));
    //$query->where('(' . $this->_db->quoteName('date_created') . ' > DATE_FORMAT(NOW(), "%Y-%m-%d") - INTERVAL 1 DAY)');
    // Just needs to be great than now...surely?
    $query->where('(' . $this->_db->quoteName('date_created') . ' > DATE_FORMAT(NOW(), "%Y-%m-%d"))');
    $query->where(
            $this->_db->quoteName('guest_email') . '=' .
            $this->_db->quote(
                    $this->_db->escape($email, true)
            )
    );
    $query->group($this->_db->quoteName('guest_email'));

    $this->_db->setQuery($query);

    try
    {

      $row = $this->_db->loadObject();
    }
    catch (Exception $e)
    {
      
    }

    return $row->count;
  }

}
