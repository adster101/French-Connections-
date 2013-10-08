<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellegacy');

/**
 * HelloWorldList Model
 */
class HelloWorldModelListing_proper extends JModelLegacy {
  /*
   * A listing property to store the listing against
   */

  public $listing = StdClass;

  /**
   * The listing id
   * @var int 
   */
  public $listing_id = '';

  /*
   * The owner ID for the user that is renewing...taken from the listing not the session scope
   */
  public $owner_id = '';

  /*
   * Whether this is a renewal or not - determined via the expiry date
   */
  public $isRenewal = '';

  /*
   * The expiry date of the listing being edited
   */
  public $expiry_date = '';

  /*
   * The review status of the property
   */
  public $isReview = '';

  /*
   * The property type payment is being calculated for.
   */
  public $property_type = '';

  /**
   * The number of days until the property expires. 0 or false meaning that the property has expired already
   * @var int
   */
  public $days_to_expiry = '';

  /**
   * Number of units on this property
   * @var int
   */
  public $total_unit_count;

  /**
   * Number of s/c unit
   * @var int
   */
  public $selfcatering_count;

  /**
   * Number of B&B units
   * @var int
   */
  public $bandb_unit_count;

  /**
   *
   * @param type $id
   * @param type $type
   */
  public function __construct($config = array()) {

    if (isset($config['id'])) {

      $this->listing_id = $config['id'];

      $this->listing = $this->getPropertyDetails();

      // Proceed to set the rest of the useful properties for this class
      // Expiry date of the listing
      $this->expiry_date = ($this->listing[0]->expiry_date) ? $this->listing[0]->expiry_date : '';

      // Number of days until the property expires
      $this->setDaysToExpiry($this->expiry_date);

      // The number of units 
      $this->setUnitCount();
    }
  }

  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   */
  protected function getPropertyDetails() {

    // Get the user ID
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Get the access control permissions in a handy array
    $canDo = HelloWorldHelper::getActions();

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
        e.review as unit_review,
        b.title,
        a.created_by,
        e.unit_id unit_id,
        e.property_id,
        d.ordering,
        e.unit_title,
        e.changeover_day,
        d.published,
        d.availability_last_updated_on,
        e.created_on,
        g.vat_status,
        base_currency,
        tariff_based_on,
        i.id as accommodation_type,
        (select count(*) from qitz3_property_images_library where version_id =  e.id) as images,
        (select count(*) from qitz3_availability where unit_id = d.id and end_date > CURDATE()) as availability,
        (select count(*) from qitz3_tariffs where unit_id = d.id and end_date > NOW()) as tariffs
      ');
    $query->from('#__property as a');
    $query->join('inner', '#__property_versions as b on (a.id = b.property_id and b.id = (select max(c.id) from #__property_versions as c where c.property_id = a.id))');
    $query->join('left', '#__unit d on d.property_id = a.id');
    $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where unit_id = d.id))');
    $query->join('left', '#__user_profile_fc g on a.created_by = g.user_id');

    // Join the property type through the property attributes table
    $query->join('innter', '#__property_attributes h on (h.property_id = d.id and h.version_id = e.id)');
    $query->join('inner', '#__attributes i on i.id = h.attribute_id');
    $query->where('i.attribute_type_id = 2 OR i.id is null)');

    $query->where('a.id = ' . (int) $this->listing_id);
    $query->order('ordering');

    // Check the user group this user belongs to.
    // Fundamental check to ensure owners only see their own listings.
    // Should this be with an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //  // If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit')) {
      $query->where('a.created_by=' . $userId);
      $query->where('d.published = 1');
    }

    $query->where('a.created_by !=0');

    $db->setQuery($query);

    if (!$results = $db->loadObjectList()) {
      return false;
    }

    return $results;
  }

  protected function setExpiryDate() {

    $this->expiry_date = ($this->listing[0]->expiry_date) ? $this->listing[0]->expiry_date : '';
  }

  /**
   * Method to return the number of days until the property is due to expire
   *
   * @param type $expiry_date
   */
  protected function setDaysToExpiry($expiry_date = '') {

    $days = '';
    $expiry_date = (!empty($expiry_date)) ? new DateTime($expiry_date) : '';


    if ($expiry_date) {
      $now = date('Y-m-d');
      $now = new DateTime($now);
      $days = $now->diff($expiry_date)->format('%R%a');
    }

    $days_to_renewal = ($days < 0) ? 0 : $days;

    $this->days_to_renewal = $days_to_renewal;
  }

  /**
   * Set the unit count for this listing.
   * 
   * If all units self-catering, then total units = units-1
   * IF all units B&B, then total units = 0;
   * If a mixture of units then...
   * 
   */
  protected function setUnitCount() {
    $bandb_unit_count = '';
    $selfcatering_unit_count = '';

    if (!empty($this->listing)) {
      // Set the unit count to be the number of units to be unit count -1 for self-catering, null for b&b as they are included
      $this->unit_count = count($this->listing);

      // Loop over the units and set a count for each type
      foreach ($this->listing as $unit) {

        if ($unit->accommodation_type == 24) {
          $bandb_unit_count++;
        } elseif ($unit->accommodation_type == 25) {
          $selfcatering_unit_count++;
        }
      }

      $this->bandb_unit_count = $bandb_unit_count;
      $this->selfcatering_unit_count = $selfcatering_unit_count;
    }
  }

  /**
   * Get the expiry date
   * @return string
   */
  public function getExpiryDate() {
    return $this->expiry_date;
  }

  /**
   * Get the days to expiry 
   * @return string
   */
  public function getDaysToExpiry() {
    return $this->expiry_date;
  }

  /**
   * Get the unit count
   * @return string
   */
  public function getUnitCount() {
    return $this->unit_count;
  }

}

