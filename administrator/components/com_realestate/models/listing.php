<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('frenchconnections.models.property.listing');

// TO DO - Merge this model with 'listingreview' model which uses JModelAdmin as base.

/**
 * HelloWorldList Model
 */
class RealEstateModelListing extends JModelList
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

    $this->setState($this->context . '.id', $id);

    $this->setState('filter.latest', true);

    // List state information.
    //parent::populateState('a.id', 'asc');
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
      if ($items[0]->review)
      {

        $query = $db->getQuery(true);
        // Set an update statement - should only be here is there are two versions...
        // Also updates the 'published on' date
        // TO DO - Make this into two updates as it doesn't seem to work as expected
        $query->update('#__realestate_property_versions');
        $query->set('
          review = CASE review
              WHEN 0 THEN -1
              WHEN 1 THEN 0
            END,
            published_on = CASE review
              WHEN 1 THEN now()
            END
        ');

        // Do this for the property ID
        $query->where('realestate_property_id=' . (int) $items[0]->id);

        $db->setQuery($query);
        $db->execute();
      }


      $query->clear();
      // Update the property review and expirty date
      $query = $db->getQuery(true);

      $query->update('#__realestate_property');
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
    $id .= ':' . $this->getState('com_realestate.listing.latest');

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
    $canDo = PropertyHelper::getActions();
    $id = $this->getState($this->context . '.id', '');
    $latest = $this->getState('com_realestate.listing.latest', true);

    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
        a.id,
        a.expiry_date,
        a.review,
        b.review as property_version_review,
        a.created_by,
        b.title,
        b.city,
        b.department,
        b.price,
        b.base_currency,
        b.use_invoice_details,
        b.latitude,
        b.longitude,
        b.first_name,
        b.surname,
        b.phone_1,
        b.email_1,
        d.vat_status,  
        (select count(*) from qitz3_realestate_property_images_library where version_id = b.id) as images
      ');
    $query->from('#__realestate_property as a');

    // Switch out on whether we want the latest or 'published' version
    if ($latest)
    {
      $query->join('inner', '#__realestate_property_versions as b on (a.id = b.realestate_property_id and b.id = (select max(c.id) from #__realestate_property_versions as c where c.realestate_property_id = a.id))');
    }
    else
    {
      $query->join('inner', '#__realestate_property_versions as b on (a.id = b.realestate_property_id and b.id = (select max(c.id) from #__realestate_property_versions as c where c.realestate_property_id = a.id and c.review = 0))');
    }

    $query->join('left', '#__user_profile_fc d on a.created_by = d.user_id');
    $query->join('left', '#__users e on a.created_by = e.id');

    $query->where('a.id = ' . (int) $id);

    // Check the user group this user belongs to.
    // Fundamental check to ensure owners only see their own listings.
    // Should this be with an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //  // If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit'))
    {
      $query->where('a.created_by=' . $userId);
    }

    if ($latest)
    {
      $query->where('b.review in (0,1)');
    }
    else
    {
      $query->where('b.review = 0');
    }

    $query->where('a.created_by !=0');

    return $query;
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
  public function getProgress($listing = array())
  {

    // Create a listing object to hold the status
    $state = new stdClass;

    $state->complete = true; // Assume listing is complete
    $state->property_detail = true; // Assume we have all property details
    $state->gallery = true; // Assume we have some images

    $state->id = $listing[0]->id; // The main listing ID
    $state->review = $listing[0]->review; // The overall review status (e.g. 0,1,2)
    $state->expiry_date = (empty($listing[0]->expiry_date)) ? '' : $listing[0]->expiry_date; // The expiry date
    $state->payment = (empty($listing[0]->expiry_date)) ? false : true; // The expiry date
    $state->days_to_renewal = PropertyHelper::getDaysToExpiry($listing[0]->expiry_date); // The calculated days to expiry

    if (!$listing[0]->use_invoice_details && (empty($listing[0]->first_name) || empty($listing[0]->surname) || empty($listing[0]->email_1) || empty($listing[0]->phone_1)))
    {
      $state->property_detail = false;
      $state->complete = false; // Listing isn't complete... use invoice details unchecked but required fields not present
    }

    // Check the property details are present and correct
    if
    (
            !$listing[0]->latitude ||
            !$listing[0]->longitude ||
            !$listing[0]->title ||
            !$listing[0]->city ||
            !$listing[0]->department ||
            !$listing[0]->price
    )
    {
      $state->property_detail = false;
      $state->complete = false;
    }

    // Check if we have some images
    if (empty($listing[0]->images))
    {
      $state->gallery = false;
      $state->complete = false;
    }

    // Determine any notices to show to the owner 
    if ($state->complete && $state->review == 1 && !empty($state->expiry_date))
    {
      $state->notice = JText::sprintf('COM_PROPERTY_NON_SUBMITTED_CHANGES', $listing[0]->id);
    }
    else if ($state->review == 2 && empty($state->expiry_date))
    { // Instantiate a new JLayoutFile instance and render the layout
      $layout = new JLayoutFile('joomla.toolbar.standard');

      $options = array(
          'text' => JText::_('COM_RENTAL_LISTING_APPROVE_CHANGES'),
          'doTask' => "Joomla.submitbutton('listing.review')",
          'btnClass' => 'btn btn-primary',
          'class' => 'icon icon-chevron-right');
      $btn = $layout->render($options);
      $state->notice = JText::_('COM_REALESTATE_LISTING_SUBMITTED_FOR_REVIEW') . $btn;
    }

    return $state;
  }

}

