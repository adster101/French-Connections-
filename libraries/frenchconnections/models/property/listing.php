<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// TO DO - Merge this model with 'listingreview' model which uses JModelAdmin as base.

/**
 * HelloWorldList Model
 */
class PropertyModelListing extends JModelList
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

          $query->update('#__unit_versions');
          $query->set('
              review = CASE review
                WHEN 0 THEN -1
                WHEN 1 THEN 0
              END,
              published_on = CASE review
                WHEN 0 THEN now()
              END
          ');
          $query->where('unit_id=' . (int) $unit->unit_id);
          $db->setQuery($query);
          $db->execute();
        }
      }

      // Update the property review and expirty date
      $query = $db->getQuery(true);

      $query->update('#__property');
      $query->set('review = 0');
      $query->set('published = 1');
      $query->set('value = ' . $db->quote('0.00'));

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

  public function sendApprovalEmail($listing = array(), $data = array())
  {

    $app = JFactory::getApplication();
    $owner_email = (JDEBUG) ? $app->getCfg('mailfrom', 'adamrifat@frenchconnections.co.uk') : $listing->email;
    $owner_name = $data['firstname'] . ' ' . $data['surname'];
    $mailfrom = $app->getCfg('mailfrom');
    $fromname = $app->getCfg('fromname');
    $body = $data['body'];
    $subject = JText::sprintf('COM_RENTAL_APPROVE_CHANGES_CONFIRMATION_SUBJECT', $data['firstname'], $listing[0]->id);
    $mail = JFactory::getMailer();

    $mail->addRecipient($owner_email, $owner_name);
    $mail->addReplyTo(array($mailfrom, $fromname));
    $mail->setSender(array($mailfrom, $fromname));
    $mail->setSubject($subject);
    $mail->setBody($body);
    $mail->isHtml(true);

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

