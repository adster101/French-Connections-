<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

jimport('simplediff.simplediff');

/**
 * HelloWorld Model
 */
class RentalModelReview extends JModelAdmin
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
        if ($text)
        {
          $html .= '<p><strong>' . JText::_('COM_ACCOMMODATION_' . strtoupper($amenity)) . '</strong>';
          $html .= JString::ucwords($text) . '</p>';
        }
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
    $model = JModelLegacy::getInstance('UnitVersions', 'RentalModel', $config = array('ignore_request' => true));

    if ($layout == 'unit')
    {

      // Get the unit versions associated with the unit being reviewed
      if (!$unit_versions = $this->getUnitVersionDetail($unitId))
      {
        Throw new Exception('Problem fetching unit version detail', 500);
      }

      foreach ($unit_versions as $key => $value)
      {
        // Get the images based on the version id we are looking at
        $images = (array_key_exists('id', $value)) ? $model->getImages($value['id']) : array();
        $versions['images'][] = $images;
      }

      if (count($versions['images']) == 2)
      {


        $simplediff = new simplediff();

        // Contains all images in the new version
        $new_version_images = array();
        $old_version_images = array();

        foreach ($versions['images'][1] as $key => $image)
        {
          $new_version_images[$image['image_file_name']] = array();
          $new_version_images[$image['image_file_name']]['position'] = $key;
          $new_version_images[$image['image_file_name']]['caption'] = $image['caption'];
        }

        foreach ($versions['images'][0] as $key => $image)
        {
          $old_version_images[$image['image_file_name']] = array();
          $old_version_images[$image['image_file_name']]['position'] = $key;
          $old_version_images[$image['image_file_name']]['caption'] = $image['caption'];
        }

        // $v contains an array of images 
        foreach ($versions['images'][0] as $key => $image)
        {
          // Deals with diffing the captions
          if (array_key_exists($image['image_file_name'], $new_version_images))
          {
            $image['deleted'] = false;
            // Image is present in both versions
            $old_caption = $image['caption'];
            $new_caption = $new_version_images[$image['image_file_name']]['caption'];

            // Get a diff on the two captions
            $diff = $simplediff->htmldiff($old_caption, $new_caption);

            // Store the diff against the new image array
            $versions['images'][1][$new_version_images[$image['image_file_name']]['position']]['diff'] = $diff;
          }

          if (!array_key_exists($image['image_file_name'], $new_version_images))
          {
            $image['deleted'] = true;
            // Image has been deleted, need to add it to new version images for completeness
            $versions['images'][0][$key] = $image;
          }
        }

        foreach ($versions['images'][1] as $key => $image)
        {
          if (!array_key_exists($image['image_file_name'], $old_version_images))
          {
            $image['added'] = true;
            $versions['images'][1][$key] = $image;
          }
        }
      }



      // Get an html based diff of all the fields.
      $unit_versions_diff = $this->getHtmlDiff($unit_versions);

      // TO DO - The below needs to be a method
      foreach ($unit_versions[0] as $k => $v)
      {
        $new_versions[$k] = array();
        $new_versions[$k][] = $unit_versions_diff[0][$k];
        $new_versions[$k][] = (!empty($unit_versions_diff[1][$k])) ? $unit_versions_diff[1][$k] : '';
        $other_array[] = $new_versions;
        $new_versions = array();
      }

      $versions['unit'] = $other_array;
    }
    else
    {
      // Fetch the latest two version of this property.
      if (!$property_versions = $this->getPropertyVersionDetail($propertyId))
      {
        Throw new Exception('Problem fetching property version detail', 500);
      }

      $property_versions = $this->decodeLocalAmenities($property_versions);

      // Get any html diffs if we have a new version
      if (!empty($property_versions[1]))
      {
        // Get an array holding the two version of the property part of the listing      
        $property_versions = $this->getHtmlDiff($property_versions);
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

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
      b.id,
      b.unit_id,
      b.property_id,
      b.unit_title,
      b.description,
      d.title as accommodation_type, 
      e.title as property_type,
      b.occupancy,
      b.single_bedrooms,
      b.double_bedrooms,
      b.triple_bedrooms,
      b.quad_bedrooms,
      b.twin_bedrooms,
      b.childrens_beds,
      b.cots,
      b.extra_beds,
      b.bathrooms,
      b.toilets,
      b.additional_price_notes,
      b.base_currency,
      b.linen_costs,
      f.title as tariff_based_on,
      g.title as changeover_day
    ');

    $query->from($db->quoteName('#__unit') . ' as a');
    $query->join('left', $db->quoteName('#__unit_versions') . ' as b on a.id = b.unit_id');
    $query->join('left', $db->quoteName('#__attributes', 'd') . ' on d.id = b.accommodation_type');
    $query->join('left', $db->quoteName('#__attributes', 'e') . ' on e.id = b.property_type');
    $query->join('left', $db->quoteName('#__attributes', 'f') . ' on f.id = b.tariff_based_on');
    $query->join('left', $db->quoteName('#__attributes', 'g') . ' on g.id = b.changeover_day');



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

  /**
   * Method takes an array containing one or two elements corresponding to different versions of
   * either a property or unit and generates an 'html' diff of the two with insertions wrapped in 
   * <ins> and deletions wrapped in <del>. If no new version then it's created but all elements
   * are set to null.
   * 
   * @param array $versions
   * @param type $keys_to_check
   * @return type
   */
  public function getHtmlDiff($versions = array())
  {

    // Get an instance of our simple diff class
    $simplediff = new simplediff();

    // 
    $old_version = $versions[0];
    $new_version = (!empty($versions[1])) ? $versions[1] : array();

    // Loop over the old version array
    foreach ($old_version as $key => $value)
    {
      if (empty($new_version[$key]))
      {
        // If we're not looking at a new version, just set it to empty
        $new_version[$key] = '';
      }
      else
      {
        $diff = $simplediff->htmldiff(strip_tags($old_version[$key]), strip_tags($new_version[$key]));
        $new_version[$key] = trim($diff);
        $old_version[$key] = strip_tags($old_version[$key]);
      }
    }

    // Update the 'diffed' versions in the version array
    $versions[1] = $new_version;
    $versions[0] = $old_version;
    return $versions;
  }

}