<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('frenchconnections.models.property.listing');

// TO DO - Merge this model with 'listingreview' model which uses JModelAdmin as base.

/**
 * HelloWorldList Model
 */
class RealEstateModelListing extends PropertyModelListing
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

    $this->setState('filter.latest', true);

    // List state information.
    parent::populateState('a.id', 'asc');
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
    $canDo = PropertyHelper::getActions();
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
        b.base_currency,
        b.use_invoice_address,
        b.latitude,
        b.longitude
        
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
      $query->where('d.published = 1');
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
    $listing->days_to_renewal = PropertyHelper::getDaysToExpiry($units[0]->expiry_date); // The calculated days to expiry

    if (!$units[0]->use_invoice_address && empty($units[0]->first_name) && empty($units[0]->surname) && empty($units[0]->email_1) && empty($units[0]->phone_1))
    {
      $listing->complete = false; // Listing isn't complete... use invoice details unchecked but required fields not present
    }

    if (
            !$units[0]->latitude ||
            !$units[0]->longitude ||
            !$units[0]->title ||
            !$units[0]->city ||
            !$units[0]->department ||
            !$units[0]->price
    )
    {
      $listing->property_detail = false;
      $listing->complete = false;
    }

    return $listing;
  }

}

