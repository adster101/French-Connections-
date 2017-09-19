<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class RentalControllerNearestTowns extends JControllerLegacy {

  /**
   * Method to find the properties assigned to a users account.
   *
   * @param   boolean  $cachable   If true, the view output will be cached
   * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return  void
   *
   * @since   2.5
   */
  public function PropertyList($cachable = false, $urlparams = false) {
   
    $return = array();
    $model = $this->getModel('PropertyList', 'RentalModel');
    $return = $model->getItems();

    // Check the data.
    if (empty($return)) {
      $return = array();
    }

    // Use the correct json mime-type
    header('Content-Type: application/json');

    // Send the response.
    echo json_encode($return);
    JFactory::getApplication()->close();
  }

  public function DepartmentTowns() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    // Get the dept ID passed in. Must be an int
    $input = JFactory::getApplication()->input;
    $dept = $input->get('dept', '', 'int');

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('id, title, latitude, longitude');
    $query->from('#__classifications');
    $query->where('parent_id = ' . (int) $dept);

    $query->order('title','asc');
    $db->setQuery($query);
    
    try {

      $return = $db->loadObjectList();
    } catch (Exception $e) {
      // TO DO - Log this exception
      return false;
    }
    
    // Use the correct json mime-type
    header('Content-Type: application/json');

    // Send the response.
    echo json_encode($return);
    JFactory::getApplication()->close();    
    
  }
}
