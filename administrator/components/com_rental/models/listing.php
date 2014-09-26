<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// TO DO - Merge this model with 'listingreview' model which uses JModelAdmin as base.

/**
 * HelloWorldList Model
 */
class RentalModelListing extends JModelList
{

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @param	string	An optional ordering field.
   * @param	string	An optional direction (asc|desc).
   *
   * @return	void
   * @since	1.6
   */
  public function populateState($ordering = null, $direction = null)
  {

    // Initialise variables
    $app = JFactory::getApplication();

    // Get the app/input gubbins
    $input = $app->input;

    // The listing ID
    $id = $input->get('id', '', 'int');

    $context = $this->context;

    $this->setState($this->context . '.id', $id);

    $extension = $app->getUserStateFromRequest('com_rentals.property.filter.extension', 'extension', 'com_rentals', 'cmd');

    $this->setState('filter.extension', $extension);
    $parts = explode('.', $extension);

    // Should be an int. No filter is null so perhaps no filter should be -1?
    $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);

    // extract the component name
    $this->setState('filter.component', $parts[0]);

    $search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
    $this->setState('filter.search', $search);

    $this->setState('filter.latest', true);

    // List state information.
    parent::populateState('a.id', 'asc');
  }

  /**
   * Controller action to publish a listing. Activated from the PFR 'approve' view
   * 
   * @param type $items
   * @return boolean
   */
  public function publishListing($items = array())
  {

    $db = JFactory::getDbo();

    try
    {

      // Start a db transaction so we can roll back if necessary
      $db->transactionStart();

      // Update the property versions 
      if ($items[0]->property_review)
      {

        $query = $db->getQuery(true);
        // Set an update statement - should only be here is there are two versions...
        // Also updates the 'published on' date
        $query->update('#__property_versions');
        $query->set('
          review = CASE review
              WHEN 0 THEN -1
              WHEN 1 THEN 0
            END,
            published_on = CASE review
              WHEN 0 THEN now()
            END
        ');

        // Do this for the property ID
        $query->where('property_id=' . (int) $items[0]->id);

        $db->setQuery($query);
        $db->execute();
      }

      // Update the unit versions
      foreach ($items as $unit)
      {
        if ($unit->unit_review)
        {
          $query = $db->getQuery(true);

          // Update the currently published unit to 'archived'
          $query->update('#__unit_versions');
          $query->set('review = -1');
          $query->where('unit_id=' . (int) $unit->unit_id);
          $query->where('review = 0');
          $db->setQuery($query);
          $db->execute();
          
          // Clear the query
          $query->clear();

          // Update the new version to published and update the published date
          $query->update('#__unit_versions');
          $query->set('review = 0, published_on = now()');
          $query->where('unit_id=' . (int) $unit->unit_id);
          $query->where('review = 1');
          $db->setQuery($query);
          $db->execute();          
          
          
        }
      }

      // Update the property review and expirty date
      $query = $db->getQuery(true);

      $query->update('#__property');
      $query->set('review = 0');
      $query->set('published = 1');
      $query->set('checked_out = \'\'');
      $query->set('checked_out_time = \'\'');
      $query->set('value = null');

      // If the expiry date is empty, and the property is being approved then implicity assume it's 
      // a new property and set the renewal date accordingly. 
      if (empty($items[0]->expiry_date))
      {
        $expiry_date = JFactory::getDate('+1 year');
        $query->set('expiry_date=' . $db->quote($expiry_date));
      }

      $query->where('id=' . (int) $items[0]->id);

      $db->setQuery($query);
      $db->execute();

      $db->transactionCommit();
    }
    catch (Exception $e)
    {

      $db->transactionRollback();

      return false;
    }

    return true;
  }

  /**
   * Controller action to publish a listing. Activated from the PFR 'approve' view
   * 
   * @param type $items
   * @return boolean
   */
  public function reject($items = array())
  {

    $db = JFactory::getDbo();

    try
    {

      // Start a db transaction so we can roll back if necessary
      $db->transactionStart();

      // Update the property review and expiry date
      $query = $db->getQuery(true);

      $query->update('#__property');
      $query->set('review = 1');
      $query->set('published = 1');
      $query->set('checked_out = \'\'');
      $query->set('checked_out_time = \'\'');

      $query->where('id=' . (int) $items[0]->id);

      $db->setQuery($query);
      $db->execute();

      $db->transactionCommit();
    }
    catch (Exception $e)
    {

      $db->transactionRollback();

      return false;
    }

    return true;
  }
  
  
  /**
   * Below can be moved into a generic helper class method
   * 
   * @param type $listing
   * @param type $body
   * @param type $subject
   * @return boolean
   */
  public function sendApprovalEmail($listing = array(), $body = '', $subject = '')
  {

    $app = JFactory::getApplication();
    
    $owner_email = (JDEBUG) ? $app->getCfg('mailfrom', 'adamrifat@frenchconnections.co.uk') : $listing[0]->email;
    $owner_name = $listing[0]->account_name;
    $mailfrom = $app->getCfg('mailfrom');
    $fromname = $app->getCfg('fromname');

    $mail = JFactory::getMailer();

    $mail->addRecipient($owner_email, $owner_name);
    $mail->addReplyTo(array($mailfrom, $fromname));
    $mail->setSender(array($mailfrom, $fromname));
    $mail->setSubject($subject);
    $mail->setBody($body);
    $mail->isHtml(true);
    
    // If this is a new property then CC a copy to an admin email (e.g. sales@) 
    if (empty($listing[0]->expiry_date))
    {
      $mail->addCC($mailfrom);
    }

    if (!$mail->Send())
    {
      return false;
    }

    return true;
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param	string		$id	A prefix for the store id.
   *
   * @return	string		A store id.
   * @since	1.6
   */
  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id .= ':' . $this->getState('filter.search');
    $id .= ':' . $this->getState('filter.extension');
    $id .= ':' . $this->getState('filter.published');
    $id .= ':' . $this->getState('com_rental.listing.latest');

    return parent::getStoreId($id);
  }

  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   */
  protected function getListQuery()
  {

    // Get the user ID
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Get the access control permissions in a handy array
    $canDo = RentalHelper::getActions();
    $id = $this->getState($this->context . '.id', '');
    $latest = $this->getState('com_rental.listing.latest', true);

    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
        a.id,
        a.expiry_date,
        a.review,
        b.review as property_review,
        b.latitude, 
        b.longitude,
        b.department,
        b.use_invoice_details,
        b.first_name,
        b.surname,
        e.review as unit_review,
        -- b.title,
        a.created_by,
        e.unit_id unit_id,
        e.property_id,
        d.ordering,
        e.unit_title,
        e.changeover_day,
        d.published,
        d.availability_last_updated_on,
        e.accommodation_type,
        e.created_on,
        g.vat_status,  
        CONCAT(g.firstname, \' \', g.surname) as account_name,
        h.email,
        b.phone_1,
        b.email_1,
        b.video_url, 
        b.lwl, 
        b.frtranslation,
        b.email_2,
        base_currency,
        tariff_based_on,
        (select count(*) from qitz3_property_images_library where version_id =  e.id) as images,
        (select count(*) from qitz3_availability where unit_id = d.id and end_date > CURDATE()) as availability,
        (select count(*) from qitz3_tariffs where unit_id = d.id and end_date > NOW()) as tariffs
      ');
    $query->from('#__property as a');

    // Switch out on whether we want the latest or 'published' version
    if ($latest)
    {
      $query->join('inner', '#__property_versions as b on (a.id = b.property_id and b.id = (select max(c.id) from #__property_versions as c where c.property_id = a.id))');
    }
    else
    {
      $query->join('inner', '#__property_versions as b on (a.id = b.property_id and b.id = (select max(c.id) from #__property_versions as c where c.property_id = a.id and c.review = 0))');
    }

    $query->join('left', '#__unit d on d.property_id = a.id');

    // Switch out on whether we want the latest or 'published' version
    if ($latest)
    {
      $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where f.unit_id = d.id))');
    }
    else
    {
      $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where f.unit_id = d.id and f.review = 0))');
    }

    $query->join('left', '#__user_profile_fc g on a.created_by = g.user_id');
    $query->join('left', '#__users h on a.created_by = h.id');

    $query->where('a.id = ' . (int) $id);
    $query->order('ordering');

    // Check the user group this user belongs to.
    // Fundamental check to ensure owners only see their own listings.
    // Should this be with an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //  // If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit'))
    {
      $query->where('a.created_by=' . $userId);
      $query->where('d.published = 1');
    }

    if ($latest)
    {
      $query->where('b.review in (0,1)');
      $query->where('e.review in (0,1)');
    }
    else
    {
      $query->where('b.review = 0');
      $query->where('e.review = 0');
    }
    
    $query->where('d.published != -2');

    $query->where('a.created_by !=0');

    return $query;
  }

  function getLanguages()
  {
    $lang = & JFactory::getLanguage();
    $languages = $lang->getKnownLanguages(JPATH_SITE);

    $return = array();
    foreach ($languages as $tag => $properties)
      $return[] = JHTML::_('select.option', $tag, $properties['name']);

    return $return;
  }

  function getTotalImages($listing = array())
  {

    $images = 0;

    foreach ($listing as $row => $unit)
    {
      $images += $unit->images;
    }

    return (int) $images;
  }

  /**
   * 
   * Method takes an array of units and determines the overall status / progress of the listing.
   * Listing needs location, unit, images, availability, tariffs and 
   * 
   * @param array   An array of units associated making up a listing
   *  
   */
  public function getProgress($units = array())
  {
    // Create a listing object to hold the status
    $listing = new stdClass;

    $listing->complete = true; // Assume listing is complete

    $listing->id = $units[0]->id; // The main listing ID
    $listing->review = $units[0]->review; // The overall review status (e.g. 0,1,2)
    $listing->expiry_date = $units[0]->expiry_date; // The expiry date
    $listing->days_to_renewal = RentalHelper::getDaysToExpiry($units[0]->expiry_date); // The calculated days to expiry

    foreach ($units as $key => $unit)
    {
      if (!$unit->availability || !$unit->tariffs || !$unit->images)
      {
        $listing->complete = false; // Listing isn't complete...
      }
    }

    if (!$units[0]->use_invoice_details && empty($units[0]->first_name) && empty($units[0]->surname) && empty($units[0]->email_1) && empty($units[0]->phone_1))
    {
      $listing->complete = false; // Listing isn't complete... use invoice details unchecked but required fields not present
    }

    if (!$units[0]->latitude || !$units[0]->longitude)
    {
      $listing->complete = false;
    }

    $listing->unit_id = $units[0]->unit_id;

    return $listing;
  }

}

