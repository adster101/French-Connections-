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
   * 
   * 
   * @param type $property_versions
   * @return string
   */
  public function decodeLocalAmenities($property_versions = array())
  {
    $lang = JFactory::getLanguage();
    $lang->load('com_accommodation', JPATH_SITE);

    foreach ($property_versions as $k => $v)
    {

      $html = '';
      $amenities = json_decode($property_versions[$k]['local_amenities']);

      // If there aren't any amenities for this just continue 
      if (empty($amenities))
      {
        continue;
      }

      // For any amenities found html them up so they can be more easily checked.
      foreach ($amenities as $amenity => $text)
      {
        $html .= '<p><strong>' . JText::_('COM_ACCOMMODATION_' . strtoupper($amenity)) . '</strong>';
        $html .= JString::ucwords($text) . '</p>';
      }

      $property_versions[$k]['local_amenities'] = $html;
    }
    return $property_versions;
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
    $input = JFactory::getApplication()->input;

    // Get the primary key set in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');
    $layout = $input->get('layout', '', 'string');
    $unitId = $input->get('unit_id', '', 'int');
    $propertyId = $input->get('property_id', '', 'int');
    $versions = array();
    $unit_versions = array();

    if ($layout == 'unit')
    {

      // An array of keys to check using the htmldiff method
      $keys_to_check = array(
          'description'
      );    // Must be reviewing a unit
      $unit_versions = $this->getUnitVersionDetail($unitId, '#__unit', '#__unit_versions', 'unit_id');
      $unit_versions['unit'] = $this->getHtmlDiff($unit_versions, $keys_to_check);


      // TO DO - The below needs to be a method
      foreach ($unit_versions[0] as $k => $v)
      {
        $new_versions[$k] = array();
        $new_versions[$k][] = $unit_versions[0][$k];
        $new_versions[$k][] = (!empty($unit_versions[1][$k])) ? $unit_versions[1][$k] : '';
        $other_array[] = $new_versions;
        $new_versions = array();
      }

      $versions['unit'] = $other_array;

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
    else
    {
      // Fetch the latest two version of this property.
      if (!$property_versions = $this->getPropertyVersionDetail($propertyId, '#__property', '#__property_versions', 'property_id'))
      {
        Throw new Exception('Problem fetching version detail', 500);
      }

      $property_versions = $this->decodeLocalAmenities($property_versions);

      // Get any html diffs if we have a new version
      if (!empty($property_versions[1]))
      {

        // An array of keys to check using the htmldiff method
        $keys_to_check = array(
            'location_details',
            'video_url',
            'security_deposit',
            'evening_meal',
            'additional_booking_info',
            'terms_and_conditions'
        );

        // Get an array holding the two version of the property part of the listing      
        $property_versions = $this->getHtmlDiff($property_versions, $keys_to_check);
      }

      // TO DO - The below needs to be a method
      foreach ($property_versions[0] as $k => $v)
      {
        $new_versions[$k] = array();
        $new_versions[$k][] = $property_versions[0][$k];
        $new_versions[$k][] = (!empty($property_versions[1][$k])) ? $property_versions[1][$k] : '';
        $other_array[] = $new_versions;
        $new_versions = array();
      }

      $versions['property'] = $other_array;
    }



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
  public function getUnitVersionDetail($recordId)
  {
    // The following keys are passed to getHtmlDiff to highlight changes in the html
    $keys_to_check = array(
        'unit_title',
        'description',
        'additional_price_notes'
    );

    $db = JFactory::getDbo();


    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
      b.unit_id,
      b.property_id,
      b.unit_title,
      b.description,
      b.accommodation_type, 
      b.property_type,
      b.occupancy,
      b.single_bedrooms,
      b.additional_price_notes,
      b.linen_costs,
      b.tariff_based_on
      ');

    $query->from($db->quoteName('#__unit') . ' as a');
    $query->join('left', $db->quoteName('#__unit_versions') . ' as b on a.id = b.unit_id');
    //$query->join('left', $db->quoteName('#__classifications') . ' c on c.id = b.country');
    //$query->join('left', $db->quoteName('#__classifications') . ' d on d.id = b.area');
    //$query->join('left', $db->quoteName('#__classifications') . ' e on e.id = b.region');
    //$query->join('left', $db->quoteName('#__classifications') . ' f on f.id = b.department');
    //$query->join('left', $db->quoteName('#__classifications') . ' g on g.id = b.city');
    //$query->join('left', $db->quoteName('#__users') . ' u on u.id = b.modified_by');
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

  /**
   * getPropertyVersionDetail = returns published and update versions of either a unit or a property listing
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
      b.property_id,
      c.title,
      d.title,
      e.title,
      f.title,
      g.title,
      b.location_details,
      b.local_amenities,
      b.latitude,
      b.longitude,
      b.distance_to_coast,
      b.video_url,
      b.booking_form,
      b.deposit,
      b.security_deposit,
      b.payment_deadline,
      b.evening_meal,
      b.additional_booking_info,
      b.terms_and_conditions,
      b.use_invoice_details,
      b.first_name,
      b.surname,
      b.address,
      b.phone_1,
      b.phone_2,
      b.phone_3,
      b.fax,
      b.email_1,
      b.email_2,
      b.website,
      b.modified_on,
      u.name
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

    $rows = $db->loadAssocList();

// Check that we have a result.
    if (empty($rows))
    {
      return false;
    }

    return $rows;
  }

  public function getHtmlDiff($versions = array(), $keys_to_check = array())
  {

    $simplediff = new simplediff();
    $new_versions = array();
    $other_array = array();



    $old_version = $versions[0];
    $new_version = (!empty($versions[1])) ? $versions[1] : array();

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