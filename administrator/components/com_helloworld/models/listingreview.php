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
  public function getPropertyDiff($recordId = null) {

    // Get the primary key and set it in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');

    // An array of keys to check using the htmldiff method
    $keys_to_check = array(
        'title', 'location_details', 'getting_there', 'video_url', 'deposit', 'security_deposit', 'evening_meal',
        'additional_booking_info', 'terms_and_conditions', 'first_name', 'surname', 'address', 'phone_1', 'phone_2',
        'phone_3', 'fax', 'email_1', 'email_2'
    );

    // Get the published version of this property.
    $old_version = $this->getPropertyVersionDetail($recordId, 0);

    // Get the unpublished version of this property
    $new_version = $this->getPropertyVersionDetail($recordId, 1);

    $new_version = $this->getItemDiff($old_version, $new_version, $keys_to_check);


    $item = JArrayHelper::toObject($new_version, 'JObject');

    $item->old = $old_version;

    return $item;
  }

  public function getUnitDiff($unitId = null) {

    $table = $this->getTable('UnitVersions', 'HelloWorldTable');
    $key = $table->getKeyName();
    $new_version = $table->getProperties();

    // Get the pk of the record from the request.
    $unitId = JFactory::getApplication()->input->getInt($key);

    $this->setState($this->getName() . '.unit_id', $unitId);

    // An array of keys to check using the htmldiff method
    $keys_to_check = array(
        'unit_title', 'description', 'internal_facilities_other', 'external_facilities_other', 'activities_other', 'additional_price_notes'
    );

    // Get the published version of this property.
    $old_version = $this->getUnitVersionDetail($unitId, 0);

    // Only need to get the new version if it's been updated.
    if ($old_version['review'] == 1) {

      // Get the unpublished version of this property
      $new = $this->getUnitVersionDetail($unitId, 1);

      $new_version = $this->getItemDiff($old_version, $new, $keys_to_check);
    }

    $item = JArrayHelper::toObject($new_version, 'JObject');

    $item->old = $old_version;

    return $item;
  }

  public function getUnitVersionDetail($unitId, $review_state = 0) {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('a.*');
    $query->from('#__unit_versions as a');
    $query->where('review = ' . (int) $review_state);
    $query->where('a.unit_id = ' . (int) $unitId);

    $query->order('id desc');

    $db->setQuery($query);

    $row = $db->loadAssoc();

    // Check that we have a result.
    if (empty($row)) {
      return false;
    }

    return $row;
  }

  /**
   * Returns a property version based on the review state passed.
   * 
   * @param type $recordId
   * @param type $review_state
   */
  public function getPropertyVersionDetail($recordId, $review_state = 0) {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('a.*, b.title as country, c.title as area, d.title as region, e.title as department, f.title as city');
    $query->from('#__property_versions as a');
    $query->where('review = ' . (int) $review_state);
    $query->where('a.parent_id = ' . (int) $recordId);
    $query->leftJoin('#__classifications b ON b.id = a.country');
    $query->leftJoin('#__classifications c ON c.id = a.area');
    $query->leftJoin('#__classifications d ON d.id = a.region');
    $query->leftJoin('#__classifications e ON e.id = a.department');
    $query->leftJoin('#__classifications f ON f.id = a.city');
    $query->order('id desc');

    $db->setQuery($query);

    $row = $db->loadAssoc();

    // Check that we have a result.
    if (empty($row)) {
      return false;
    }

    return $row;
  }

  public function getItemDiff($old_version = array(), $new_version = array(), $keys_to_check = array()) {

    $simplediff = new simplediff();

    // Need to load the new version details here to replace those loaded here.
    foreach ($old_version as $key => $value) {

      if (in_array($key, $keys_to_check)) {
        $diff = $simplediff->htmldiff(strip_tags($old_version[$key]), strip_tags($new_version[$key]));

        $new_version[$key] = $diff;
      }
    }

    return $new_version;
  }

  public function getUnits() {

    $db = JFactory::getDbo();
    
    // Get the primary key and set it in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');

    try {

      // Get the node and children as a tree.
      $query = $this->_db->getQuery(true);
      $select = 'unit_title,b.review as review_unit,c.review as review_property, a.id as unit_id,occupancy,parent_id,(single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms';
      $query->select($select)
              ->from('#__unit a')
              ->join('left', '#__unit_versions b on a.id = b.unit_id')
              ->join('left', '#__property c on c.id = a.property_id')
              ->where('a.property_id = ' . (int) $recordId)
              ->where('a.published = 1')
              ->where('b.review = 0')
              ->order('ordering');

      $db->setQuery($query);
      $row = $db->loadObjectList();
      
      return $row;
      
    } catch (Exception $e) {
      // Log the exception and return false
      
      return false;
    }
  }

}