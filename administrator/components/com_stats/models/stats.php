<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellegacy');

// Import the graph gubbins
jimport('pchart.class.pDraw');
jimport('pchart.class.pImage');
jimport('pchart.class.pData');

/**
 * HelloWorld Model
 */
class StatsModelStats extends JModelList {
  /*
   * The property id for the stats we are retrieving
   */

  protected $id = '';

  public function getGraphData() {

    $input = JFactory::getApplication()->input;
    // Get the id from the model state
    if ($this->getState('filter.search')) {
      $id = (int) $this->getState('filter.search');
    } elseif ($this->getState('filter.id')) {
      $id = (int) $this->getState('filter.id');
    } else {
      $id = $input->get('id','','int');
    }

    $date_range = $this->getState('filter.date_range', '-1 year');

    // Set up an array to hold the series data
    $graph_data = array();

    // Get the various data to populate the report
    $graph_data['views'] = $this->getData($id, '#__property_views', $date_range);
    $graph_data['enquiries'] = $this->getData($id, '#__enquiries', $date_range);
    $graph_data['clicks'] = $this->getData($id, '#__website_views', $date_range);
    $graph_data['reviews'] = $this->getData($id, '#__reviews', $date_range );


    return $graph_data;
  }

  public function preprocessForm(\JForm $form, $data, $group = 'content') {

    $user = JFactory::getUser();

    $groups = JAccess::getGroupsByUser($user->id, false);

    if (in_array(10, $groups)) {
      $form->removeField('search', 'filter');
    } else {
      $form->removeField('id', 'filter');
    }

    // Add or remove the search box depending on the user privileges.
    // If owner and not admin remove the box, retain the property dropdown
    // If admin and not owner remove the dropdown and show the search box

    parent::preprocessForm($form, $data, $group);
  }

  public function populateState() {

    parent::populateState();

    //$input = JFactory::getApplication()->input;
    //$id = $input->get('id', '', 'int');
    //$this->setState('stats.id', $id);
  }

  public function getData($id = '', $table = '', $range = '') {

    // Add in the number of page view this property has had in the last twelve months...

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('
       count(id) as count
     ');
    $query->from($table);
    $query->where('property_id = ' . (int) $id);
    if ($range) {
      $now = date('Y-m-d');
      $last_year = strtotime((string) $range, strtotime($now));
      $query->where('date_created > ' . $db->quote(date('Y-m-d', $last_year)));
    }
    $db->setQuery($query);

    try {
      $rows = $db->loadRow();
    } catch (Exception $e) {
      return false;
    }

    return $rows;
  }

  public function loadFormData() {
    
    $data = JFactory::getApplication()->getUserState($this->context, new stdClass);
    
    $input = JFactory::getApplication()->input;
    
    if ($input->get('id','','int')) {
      $id = $input->get('id','','int');
      $data->filter = array('id'=>$id);
    }

    
    
    
    return $data;
  }
  
  
}
