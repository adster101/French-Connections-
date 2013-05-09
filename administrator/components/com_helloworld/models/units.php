<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * HelloWorld Model
 */
class HelloWorldModelUnits extends JModelList {

  public function getListQuery() {

    // Get the user ID
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Get the app/input gubbins
    $app = JFactory::getApplication();
    $input = $app->input;
    // The listing ID
    $id = $input->get('id', '', 'int');

    // Get the access control permissions in a handy array
    $canDo = HelloWorldHelper::getActions();

    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Initialise the query.
    $query = $this->_db->getQuery(true);
    $query->select('
        id,
        parent_id,
        ordering,
        unit_title,
        (select count(*) from #__property_attributes where property_id = pu.id) as facilities,
        (select count(*) from qitz3_availability where id = pu.id and end_date > CURDATE()) as availability,
        (select count(*) from qitz3_tariffs where id = pu.id and end_date > CURDATE()) as tariffs,
        (select count(*) from qitz3_property_images_library where property_id =  pu.id) as images
      ');
    $query->from('#__property_units as pu');

    // Add the search tuple to the query.
    $query->where('parent_id = ' . (int) $id);

    return $query;
  }

}
