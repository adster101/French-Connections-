<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * Images Model
 */
class HelloWorldModelImages extends JModelList {
  
  /**
   * Method to get a JDatabaseQuery object for retrieving the data set from a database.
   *
   * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
   *
   * @since   12.2
   */
  public function getListQuery() {

    $id = $this->getState('version_id','');

    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      id,
      unit_id,
      image_file_name,
      caption,
      ordering,
      version_id
    ');
    $query->from('#__property_images_library');

    $query->where('version_id = ' . (int) $id);

    $query->order('ordering', 'asc');

    return $query;
  }

}
