<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

jimport('simplediff.simplediff');

/**
 * HelloWorld Model
 */
class RentalModelListingReview extends JModelAdmin
{

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
    $form = $this->loadForm('com_rental.approve_draft', 'approve_draft', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Get the message text to bind with the form
   */
  public function loadFormData()
  {

    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');

    // Get the owner details etc
    $table = $this->getTable('Property', 'RentalTable');

    $property = $table->load($recordId);

    if (!$property)
    {

      Throw new Exception('Problem loading property details', 500);
    }

    $userId = ($table->created_by) ? $table->created_by : 0;

    $user = JFactory::getUser($userId);


    $data['body'] = JText::sprintf('COM_RENTAL_HELLOWORLD_APPROVE_CHANGES_EMAIL_BODY', $user->name, $recordId, 'asdasd');

    return $data;
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
  public function getTable($type = 'PropertyVersions', $prefix = 'RentalTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * 
   * 
   * 
   */
  public function getListingDiff($recordId = null)
  {

    // Get the primary key set in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');

    $input = JFactory::getApplication()->input;
    $unitId = $input->get('unit_id', '', 'int');
    $propertyId = $input->get('property_id', '', 'int');
    $versions = array();
    $unit_versions = array();

    if (!empty($unitId))
    {

      // Must be reviewing a unit
      $unit_versions = $this->getVersionDetail($unitId, '#__unit', '#__unit_versions', 'unit_id');

      // An array of keys to check using the htmldiff method
      $keys_to_check = array(
          'unit_title', 'description', 'internal_facilities_other', 'external_facilities_other', 'activities_other', 'additional_price_notes'
      );

      $versions['unit'] = $this->getItemDiff($unit_versions, $keys_to_check);

      /*
       * Loop over the versions and add the images and facilities for each. Translations as well?
       */
      $model = JModelLegacy::getInstance('UnitVersions', 'RentalModel', $config = array('ignore_request' => true));

      foreach ($versions['unit'] as $key => $value)
      {

        /*
         * Get the images based on the version id we are looking at
         */

        $images = (array_key_exists('id', $value)) ? $model->getImages($value['id']) : array();

        if (!$images)
        {
          continue;
        }

        $versions['images'][$value['id']] = $images;
      }
    }



    $property_versions = $this->getPropertyVersionDetail($propertyId, '#__property', '#__property_versions', 'property_id');

    // An array of keys to check using the htmldiff method
    $keys_to_check = array(
        'title', 'location_details', 'getting_there', 'video_url', 'deposit', 'security_deposit', 'evening_meal',
        'additional_booking_info', 'terms_and_conditions', 'first_name', 'surname', 'address', 'phone_1', 'phone_2',
        'phone_3', 'fax', 'email_1', 'email_2'
    );


    /*
     *  $versions contains one or two records
     */
    if (!$property_versions)
    {
      // OOoops
      return false;
    }

    $versions['property'] = $this->getItemDiff($property_versions, $keys_to_check);

    return $versions;
  }

  /**
   * getVersionDetail = returns published and update versions of either a unit or a property listing
   * 
   * @param type $recordId
   * @param type $table1
   * @param type $table2
   * @param type $join_field
   * @return mixed 
   * 
   */
  public function getPropertyVersionDetail($recordId)
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
      b.property_id as `PRN`,
      b.id as `Version id`,
      c.title as `Country`,
      d.title as `Area`,
      e.title as `Region`,
      f.title as `Department`,
      g.title as `Nearest town`,
      b.location_details as `Location details`,
      b.local_amenities as `Local amenities`,
      b.latitude as `Latitude`,
      b.longitude as `Longitude`,
      b.distance_to_coast as `Distance to coast`,
      b.video_url as `Youtube link`,
      b.booking_form as `Show booking form`,
      b.deposit as `Deposit`,
      b.security_deposit as `Security deposit`,
      b.payment_deadline as `Payment deadline`,
      b.evening_meal as `Evening meal`,
      b.additional_booking_info as `Additional booking information`,
      b.terms_and_conditions as `Terms and conditions`,
      b.use_invoice_details as `Use invoice details for enquiries`,
      b.first_name as `Forename`,
      b.surname as `Surname`,
      b.address as `Address`,
      b.phone_1 as `Telephone 1`,
      b.phone_2 as `Telephone 2`,
      b.phone_3 as `Telephone 3`,
      b.fax as `Fax!?`,
      b.email_1 `Primary email`,
      b.email_2 as `Secondary email`,
      b.website as `Website`,
      b.modified_on as `Date updated`,
      u.name as  `Modified by`
    ');

    $query->from($db->quoteName('#__property') . ' as a');
    $query->join('left', $db->quoteName('#__property_versions') . ' as b on a.id = b.property_id');
    $query->join('left', $db->quoteName('#__classifications') . ' c on c.id = b.country');
    $query->join('left', $db->quoteName('#__classifications') . ' d on d.id = b.area');
    $query->join('left', $db->quoteName('#__classifications') . ' e on e.id = b.region');
    $query->join('left', $db->quoteName('#__classifications') . ' f on f.id = b.department');
    $query->join('left', $db->quoteName('#__classifications') . ' g on g.id = b.city');
    $query->join('left', $db->quoteName('#__users') . ' u on u.id = b.modified_by');
    $query->where('a.id = ' . (int) $recordId);
    $query->where('b.review in (0,1)');

    $db->setQuery($query);

    $row = $db->loadAssocList();

    // Check that we have a result.
    if (empty($row))
    {
      return false;
    }

    return $row;
  }

    public function getItemDiff($versions = array(), $keys_to_check = array())
  {

    $simplediff = new simplediff();

    // If we only have one version then don't bother with the difference
    if (count($versions) < 2)
    {
      $versions[] = array();

      return $versions;
    }

    $old_version = $versions[0];
    $new_version = $versions[1];

    // Need to load the new version details here to replace those loaded here.
    foreach ($old_version as $key => $value)
    {
      if (in_array($key, $keys_to_check))
      {
        $diff = $simplediff->htmldiff(strip_tags($old_version[$key]), strip_tags($new_version[$key]));
        $new_version[$key] = $diff;
      }
    }

    $versions[1] = $new_version;

    return $versions;
  }

}