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

    $context = $this->context;

    $this->setState($this->context . '.id', $id);

    $this->setState('filter.latest', true);

    // List state information.
    //parent::populateState('a.id', 'asc');
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
        a.created_by,
        b.title,
        b.city,
        b.department,
        b.price,
        b.base_currency,
        b.use_invoice_details,
        b.latitude,
        b.longitude,
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
    
    if (!$listing[0]->use_invoice_details && empty($listing[0]->first_name) && empty($listing[0]->surname) && empty($listing[0]->email_1) && empty($listing[0]->phone_1))
    {
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

    return $state;
  }

}

