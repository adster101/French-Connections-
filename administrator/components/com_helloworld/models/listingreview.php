<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

jimport('simplediff.simplediff');

/**
 * HelloWorld Model
 */
class HelloWorldModelListingReview extends JModelAdmin {

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = false) {
    // Get the form.
    $form = $this->loadForm('com_helloworld.listingreview', 'listingreview', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }
    return $form;
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
  public function getTable($type = 'PropertyVersions', $prefix = 'HelloWorldTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Returns a complete set of data for a property record.
   * 
   * 
   */
  public function getListingDiff($recordId = null) {

    // Get the primary key set in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');

    $input = JFactory::getApplication()->input;
    $unitId = $input->get('unit_id', '', 'int');
    $propertyId = $input->get('property_id', '', 'int');
    $versions = array();
    $unit_versions = array();

    if (!empty($unitId)) {

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
      $model = JModelLegacy::getInstance('UnitVersions', 'HelloWorldModel', $config=array('ignore_request'=>true)); 
      
      foreach ($versions['unit'] as $key => $value) {
        
        /*
         * Get the images based on the version id we are looking at
         */
         
        $images = (array_key_exists('id', $value)) ? $model->getImages($value['id']) : array();
        
        if (!$images) {
          continue;
        }
        
        $versions['images'][$value['id']] = $images;
        
      }
      
      
      
    }



    $property_versions = $this->getVersionDetail($propertyId, '#__property', '#__property_versions', 'property_id');

    // An array of keys to check using the htmldiff method
    $keys_to_check = array(
        'title', 'location_details', 'getting_there', 'video_url', 'deposit', 'security_deposit', 'evening_meal',
        'additional_booking_info', 'terms_and_conditions', 'first_name', 'surname', 'address', 'phone_1', 'phone_2',
        'phone_3', 'fax', 'email_1', 'email_2'
    );


    // $versions contains one or two records
    if (!$property_versions) {
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
  public function getVersionDetail($recordId, $table1 = '', $table2 = '', $join_field) {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('b.*');
    $query->from($db->quoteName($table1) . ' as a');
    $query->join('left', $db->quoteName($table2) . ' as b on a.id = b.' . $join_field);
    $query->where('a.id = ' . (int) $recordId);
    $query->where('b.review in (0,1)');

    $db->setQuery($query);

    $row = $db->loadAssocList();

    // Check that we have a result.
    if (empty($row)) {
      return false;
    }

    return $row;
  }

  public function getItemDiff($versions = array(), $keys_to_check = array()) {

    $simplediff = new simplediff();

    // If we only have one version then don't bother with the difference
    if (count($versions) < 2) {
      $versions[] = array();

      return $versions;
    }

    $old_version = $versions[0];
    $new_version = $versions[1];

    // Need to load the new version details here to replace those loaded here.
    foreach ($old_version as $key => $value) {
      if (in_array($key, $keys_to_check)) {
        $diff = $simplediff->htmldiff(strip_tags($old_version[$key]), strip_tags($new_version[$key]));
        $new_version[$key] = $diff;
      }
    }

    $versions[1] = $new_version;

    return $versions;
  }

}